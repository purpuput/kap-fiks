<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\AuditReportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentManagementController extends Controller
{
    /**
     * Label tampilan tipe dokumen
     */
    protected array $documentTypeLabels = [
        'spk'                      => 'SPK',
        'surat_tugas'              => 'Surat Tugas',
        'invoice'                  => 'Invoice',
        'laporan_keuangan'         => 'Laporan Keuangan',
        'kertas_kerja'             => 'Kertas Kerja',
        'berita_acara_pemeriksaan' => 'Berita Acara Pemeriksaan',
        'data_klien'               => 'Data Klien',
        'surat_pernyataan_klien'   => 'Surat Pernyataan Klien',
        'management_letter'        => 'Management Letter',
        'laporan_audit'            => 'Laporan Audit',
    ];

    /**
     * Halaman daftar klien
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $clients = Client::withCount('documentManagements')
            ->when($search, function ($query) use ($search) {
                $query->where('institution_name', 'like', "%{$search}%");
            })
            ->where('status', 'aktif')
            ->having('document_managements_count', '>', 0)
            ->paginate(12);

        return view('document_managements.index', compact(
            'clients',
            'search'
        ));
    }

    /**
     * Detail dokumen berdasarkan klien
     */
    public function show(Client $client, Request $request)
    {
        $documentType = $request->input('document_type');

        $query = AuditReportFile::with([
            'auditReport',
            'approvedBy',
            'review'
        ])
            ->whereHas('review', function ($q) {
                $q->where('status', 'approved');
            })
            ->whereHas('auditReport', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            });

        if ($documentType) {
            $query->where('document_type', $documentType);
        }

        $documents = $query
            ->latest('approved_at')
            ->paginate(15);

        $documentTypeLabels = $this->documentTypeLabels;

        return view('document_managements.show', compact(
            'client',
            'documents',
            'documentType',
            'documentTypeLabels'
        ));
    }

    /**
     * Download dokumen
     */
    public function download(AuditReportFile $documentManagement)
    {
        // Pengecekan otorisasi auditor
        if (Auth::user()->role === 'auditor') {
            $hasAccess = $documentManagement->auditReport->client->auditors()->where('users.id', Auth::id())->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk dokumen ini.');
            }
        }

        if (empty($documentManagement->file) || !Storage::disk('local')->exists($documentManagement->file)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return Storage::disk('local')->download(
            $documentManagement->file,
            $documentManagement->document_name
        );
    }

    /**
     * Preview dokumen
     */
    public function preview(AuditReportFile $documentManagement)
    {
        // Pengecekan otorisasi auditor
        if (Auth::user()->role === 'auditor') {
            $hasAccess = $documentManagement->auditReport->client->auditors()->where('users.id', Auth::id())->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk dokumen ini.');
            }
        }

        if (empty($documentManagement->file) || !Storage::disk('local')->exists($documentManagement->file)) {
            return response('<div style="text-align:center; padding: 50px; font-family: sans-serif; color: #666;"><h3>File Tidak Ditemukan</h3><p>File fisik tidak ada di server karena ini adalah data dummy atau file telah dihapus.</p></div>', 404);
        }

        $path = Storage::disk('local')->path($documentManagement->file);
        return response()->file($path);
    }
}
