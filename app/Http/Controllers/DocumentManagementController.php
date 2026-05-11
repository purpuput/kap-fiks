<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DocumentManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentManagementController extends Controller
{
    /**
     * Label tampilan untuk tipe dokumen
     */
    protected array $documentTypeLabels = [
        'spk'                      => 'SPK (Surat Perintah Kerja)',
        'surat_tugas'              => 'Surat Tugas',
        'invoice'                  => 'Invoice',
        'laporan_keuangan'         => 'Laporan Keuangan',
        'kertas_kerja'             => 'Kertas Kerja',
        'berita_acara_pemeriksaan' => 'Berita Acara Pemeriksaan',
        'data_klien'               => 'Data yang Diterima dari Klien',
        'surat_pernyataan_klien'   => 'Surat Pernyataan Klien',
        'management_letter'        => 'Management Letter Temuan',
        'laporan_audit'            => 'Laporan Audit',
    ];

    public function index(Request $request)
    {
        $search        = $request->input('search');
        $clientId      = $request->input('client_id');
        $documentType  = $request->input('document_type');

        $query = DocumentManagement::with([
            'auditReport.client',
            'archivedBy',
        ]);

        if ($search) {
            $query->where('document_name', 'like', "%{$search}%");
        }

        if ($clientId) {
            $query->whereHas('auditReport', fn ($q) => $q->where('client_id', $clientId));
        }

        if ($documentType) {
            $query->where('document_type', $documentType);
        }

        $documents           = $query->latest('archived_at')->paginate(15);
        $clients             = Client::where('status', 'aktif')->get();
        $documentTypeLabels  = $this->documentTypeLabels;

        return view('document_managements.index', compact(
            'documents',
            'clients',
            'documentTypeLabels',
            'search',
            'clientId',
            'documentType'
        ));
    }

    public function download(DocumentManagement $documentManagement)
    {
        return Storage::disk('public')->download(
            $documentManagement->file,
            $documentManagement->document_name
        );
    }
}
