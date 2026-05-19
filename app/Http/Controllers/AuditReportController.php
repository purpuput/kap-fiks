<?php

namespace App\Http\Controllers;

use App\Models\AuditReport;
use App\Models\AuditReportFile;
use App\Models\Client;
use App\Models\DocumentManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuditReportController extends Controller
{
    /**
     * Tipe dokumen audit
     */
    protected array $documentTypes = [

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
     * List laporan
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $status = $request->status;

        $query = AuditReport::with([

            'client',

            'creator',

            'reviewer',

            'files'
        ]);

        // Auditor hanya melihat laporan miliknya
        if (Auth::user()->role === 'auditor') {

            $query->where(
                'created_by',
                Auth::id()
            );
        }

        // Search
        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )

                    ->orWhere(
                        'report_number',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        // Filter status
        if ($status) {

            $query->where(
                'status',
                $status
            );
        }

        $reports = $query
            ->latest()
            ->paginate(10);

        return view(
            'audit_reports.index',
            compact(

                'reports',

                'search',

                'status'
            )
        );
    }

    /**
     * Detail laporan
     */
    public function show(AuditReport $auditReport)
    {
        // Auditor hanya boleh melihat laporan miliknya
        if (

            Auth::user()->role === 'auditor' &&

            $auditReport->created_by !== Auth::id()

        ) {

            abort(403);
        }

        $auditReport->load([

            'client',

            'creator',

            'reviewer',

            'files'
        ]);

        $documentTypes = $this->documentTypes;

        $filesByType = $auditReport->files
            ->keyBy('document_type');

        return view(
            'audit_reports.show',
            compact(

                'auditReport',

                'documentTypes',

                'filesByType'
            )
        );
    }

    /**
     * Form create
     */
    public function create()
    {
        // Hanya auditor
        abort_if(

            Auth::user()->role !== 'auditor',

            403
        );

        // Client assigned
        $clients = Auth::user()
            ->clients()
            ->with('auditors')
            ->where(
                'approval_status',
                'disetujui'
            )
            ->where(
                'status',
                'aktif'
            )
            ->get();

        $documentTypes = $this->documentTypes;

        return view(
            'audit_reports.create',
            compact(

                'clients',

                'documentTypes'
            )
        );
    }

    /**
     * Store laporan
     */
    public function store(Request $request)
    {
        // Hanya auditor
        abort_if(

            Auth::user()->role !== 'auditor',

            403
        );

        $fileRules = [];

        // Validasi file
        foreach (
            array_keys($this->documentTypes)
            as $type
        ) {

            $mimes = ($type === 'data_klien')

                ? 'mimes:zip,rar,7z,pdf,doc,docx,xls,xlsx|max:102400'

                : 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:51200';

            $fileRules["files.{$type}"] =
                "required|file|{$mimes}";
        }

        // Validasi form
        $request->validate(array_merge([

            'client_id' => 'required|exists:clients,id',

            'audit_date' => 'required|date',

        ], $fileRules));

        // Client
        $client = Client::findOrFail(
            $request->client_id
        );

        // Hanya bisa membuat laporan untuk klien yang di-assign ke mereka
        abort_if(
            !$client->auditors()->where('users.id', Auth::id())->exists(),
            403
        );

        // Generate nomor laporan
        $lastReport = AuditReport::latest()->first();

        $number = $lastReport

            ? ((int) substr(
                $lastReport->report_number,
                -4
            )) + 1

            : 1;

        $reportNumber = 'LAP-' .
            date('Y') . '-' .
            str_pad(
                $number,
                4,
                '0',
                STR_PAD_LEFT
            );

        // Simpan laporan
        $report = AuditReport::create([

            'client_id' => $request->client_id,

            'report_number' => $reportNumber,

            'title' => 'Laporan Audit - ' .
                $client->institution_name,

            // Otomatis dari client
            'audit_type' => $client->audit_type,

            'audit_date' => $request->audit_date,

            'findings' => 'Lihat pada dokumen lampiran.',
            'recommendations' => 'Lihat pada dokumen lampiran.',

            'status' => 'review',

            'created_by' => Auth::id(),
        ]);

        // Upload file
        foreach (
            array_keys($this->documentTypes)
            as $type
        ) {

            if (
                $request->hasFile("files.{$type}")
            ) {

                $file = $request->file(
                    "files.{$type}"
                );

                $path = $file->store(

                    "audit_reports/{$report->id}",

                    'public'
                );

                AuditReportFile::create([

                    'audit_report_id' => $report->id,

                    'document_name' =>
                    $file->getClientOriginalName(),

                    'document_type' => $type,

                    'file' => $path,

                    'version' => 1,

                    'is_active' => true,

                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return redirect()
            ->route('audit_reports.index')
            ->with(
                'success',
                'Laporan audit berhasil dibuat.'
            );
    }

    /**
     * Form edit
     */
    public function edit(AuditReport $auditReport)
    {
        // Hanya auditor
        abort_if(

            Auth::user()->role !== 'auditor',

            403
        );

        // Hanya laporan sendiri
        abort_if(

            $auditReport->created_by !== Auth::id(),

            403
        );

        // Final tidak bisa edit
        if (
            $auditReport->status === 'disetujui'
        ) {

            return back()->with(

                'error',

                'Laporan tidak dapat diedit.'
            );
        }

        $clients = Auth::user()
            ->clients()
            ->with('auditors')
            ->where(
                'approval_status',
                'disetujui'
            )
            ->where(
                'status',
                'aktif'
            )
            ->get();

        $documentTypes = $this->documentTypes;

        $filesByType = $auditReport->files
            ->keyBy('document_type');

        return view(
            'audit_reports.edit',
            compact(

                'auditReport',

                'clients',

                'documentTypes',

                'filesByType'
            )
        );
    }

    /**
     * Update laporan
     */
    public function update(
        Request $request,
        AuditReport $auditReport
    ) {
        // Hanya auditor
        abort_if(

            Auth::user()->role !== 'auditor',

            403
        );

        // Hanya laporan sendiri
        abort_if(

            $auditReport->created_by !== Auth::id(),

            403
        );

        // Final tidak bisa edit
        if (
            $auditReport->status === 'disetujui'
        ) {

            return back()->with(

                'error',

                'Laporan tidak dapat diedit.'
            );
        }

        $fileRules = [];

        $existingFiles = $auditReport->files
            ->keyBy('document_type');

        // Validasi file
        foreach (
            array_keys($this->documentTypes)
            as $type
        ) {

            $required = isset(
                $existingFiles[$type]
            )
                ? 'nullable'
                : 'required';

            $mimes = ($type === 'data_klien')

                ? 'mimes:zip,rar,7z,pdf,doc,docx,xls,xlsx|max:102400'

                : 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:51200';

            $fileRules["files.{$type}"] =
                "{$required}|file|{$mimes}";
        }

        // Validasi form
        $request->validate(array_merge([

            'client_id' => 'required|exists:clients,id',

            'audit_date' => 'required|date',

        ], $fileRules));

        // Client
        $client = Client::findOrFail(
            $request->client_id
        );

        // Update laporan
        $auditReport->update([

            'client_id' => $request->client_id,

            'title' => 'Laporan Audit - ' .
                $client->institution_name,

            'audit_type' => $client->audit_type,

            'audit_date' => $request->audit_date,
        ]);

        // Update file
        foreach (
            array_keys($this->documentTypes)
            as $type
        ) {

            if (
                $request->hasFile("files.{$type}")
            ) {

                $file = $request->file(
                    "files.{$type}"
                );

                $existing =
                    $existingFiles[$type] ?? null;

                // Hapus file lama
                if ($existing) {

                    Storage::disk('public')
                        ->delete($existing->file);

                    $existing->delete();

                    // Hapus archive lama
                    DocumentManagement::where(

                        'audit_report_id',
                        $auditReport->id

                    )
                        ->where(
                            'document_type',
                            $type
                        )
                        ->delete();
                }

                // Upload file baru
                $path = $file->store(

                    "audit_reports/{$auditReport->id}",

                    'public'
                );

                AuditReportFile::create([

                    'audit_report_id' =>
                    $auditReport->id,

                    'document_name' =>
                    $file->getClientOriginalName(),

                    'document_type' => $type,

                    'file' => $path,

                    'version' => $existing
                        ? $existing->version + 1
                        : 1,

                    'is_active' => true,

                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        // Submit ulang setelah revisi
        if (
            $auditReport->fresh()->status === 'revisi'
        ) {

            $auditReport->update([

                'status' => 'review',

                'revision_note' => null,
            ]);
        }

        return redirect()
            ->route('audit_reports.index')
            ->with(
                'success',
                'Laporan berhasil diperbarui.'
            );
    }

    /**
     * Hapus laporan
     */
    public function destroy(AuditReport $auditReport)
    {
        // Hanya auditor
        abort_if(

            Auth::user()->role !== 'auditor',

            403
        );

        // Hanya laporan sendiri
        abort_if(

            $auditReport->created_by !== Auth::id(),

            403
        );

        // Final tidak bisa dihapus
        if (
            $auditReport->status === 'disetujui'
        ) {

            return back()->with(

                'error',

                'Laporan tidak dapat dihapus.'
            );
        }

        // Hapus storage
        foreach (
            $auditReport->files
            as $file
        ) {

            Storage::disk('public')
                ->delete($file->file);
        }

        $auditReport->delete();

        return redirect()
            ->route('audit_reports.index')
            ->with(
                'success',
                'Laporan berhasil dihapus.'
            );
    }

    /**
     * Approve file (dokumen audit per item)
     */
    public function approveFile(Request $request, AuditReportFile $file)
    {
        // Hanya pimpinan
        abort_if(Auth::user()->role !== 'pimpinan', 403);

        $request->validate([
            'status' => 'required|in:lengkap,belum lengkap',
            'revision_note' => 'required_if:status,belum lengkap|nullable|string',
        ]);

        $file->update([
            'status' => $request->status,
            'revision_note' => $request->status === 'belum lengkap' ? $request->revision_note : null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Status dokumen berhasil diperbarui.');
    }

    /**
     * Approve laporan
     */
    public function approve(AuditReport $auditReport)
    {
        // Hanya pimpinan
        abort_if(

            Auth::user()->role !== 'pimpinan',

            403
        );

        $auditReport->load('files');

        // Approve
        $auditReport->update([

            'status' => 'disetujui',

            'reviewed_by' => Auth::id(),

            'reviewed_at' => now(),

            'revision_note' => null,
        ]);

        // Auto archive
        foreach (
            $auditReport->files
            as $file
        ) {

            DocumentManagement::updateOrCreate(

                [
                    'audit_report_id' =>
                    $auditReport->id,

                    'document_type' =>
                    $file->document_type,
                ],

                [
                    'document_name' =>
                    $file->document_name,

                    'file' =>
                    $file->file,

                    'archived_by' =>
                    Auth::id(),

                    'archived_at' =>
                    now(),
                ]
            );
        }

        return back()->with(

            'success',

            'Laporan berhasil disetujui.'
        );
    }

    /**
     * Revisi laporan
     */
    public function revisi(
        Request $request,
        AuditReport $auditReport
    ) {
        // Hanya pimpinan
        abort_if(

            Auth::user()->role !== 'pimpinan',

            403
        );

        $request->validate([

            'revision_note' =>
            'required|string|min:10',
        ]);

        // Revisi laporan
        $auditReport->update([

            'status' => 'revisi',

            'revision_note' =>
            $request->revision_note,

            'reviewed_by' => Auth::id(),

            'reviewed_at' => null,
        ]);

        return back()->with(

            'success',

            'Laporan dikembalikan untuk revisi.'
        );
    }

    /**
     * Download file
     */
    public function download(AuditReportFile $file)
    {
        if (empty($file->file) || !Storage::disk('public')->exists($file->file)) {
            return back()->with('error', 'File tidak ditemukan di server (Data dummy/hilang).');
        }

        return Storage::disk('public')->download(
            $file->file,
            $file->document_name
        );
    }

    /**
     * Preview file
     */
    public function preview(AuditReportFile $file)
    {
        if (empty($file->file) || !Storage::disk('public')->exists($file->file)) {
            return response('<div style="text-align:center; padding: 50px; font-family: sans-serif; color: #666;"><h3>File Tidak Ditemukan</h3><p>File fisik tidak ada di server karena ini adalah data dummy atau file telah dihapus.</p></div>', 404);
        }

        $path = storage_path('app/public/' . $file->file);
        return response()->file($path);
    }
}
