<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DocumentManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $query = DocumentManagement::with([
            'auditReport',
            'archivedBy'
        ])
            ->whereHas('auditReport', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            });

        if ($documentType) {
            $query->where('document_type', $documentType);
        }

        $documents = $query
            ->latest('archived_at')
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
    public function download(DocumentManagement $documentManagement)
    {
        return Storage::download(
            'public/' . $documentManagement->file,
            $documentManagement->document_name
        );
    }

    /**
     * Preview dokumen
     */
    public function preview(DocumentManagement $documentManagement)
    {
        $path = storage_path('app/public/' . $documentManagement->file);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
