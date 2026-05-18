<?php

namespace App\Http\Controllers;

use App\Models\AuditReport;
use App\Models\AuditReportFile;
use App\Models\Client;
use App\Models\DocumentManagement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuditReportController extends Controller
{
    /**
     * 10 tipe dokumen wajib laporan audit
     */
    protected array $documentTypes = [
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

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = AuditReport::with(['client', 'createdBy', 'auditors', 'files']);

        // Jika user adalah auditor, hanya tampilkan laporan di mana ia terlibat
        if (Auth::user()->role === 'auditor') {
            $query->whereHas('auditors', function ($q) {
                $q->where('users.id', Auth::id());
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('report_number', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(10);

        return view('audit_reports.index', compact('reports', 'search', 'status'));
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(AuditReport $auditReport)
    {
        $auditReport->load(['client', 'createdBy', 'auditors', 'files.uploader']);

        $documentTypes = $this->documentTypes;
        $filesByType   = $auditReport->files->keyBy('document_type');

        return view('audit_reports.show', compact('auditReport', 'documentTypes', 'filesByType'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $clients       = Client::where('status', 'aktif')->get();
        $auditors      = User::where('role', 'auditor')->get();
        $documentTypes = $this->documentTypes;

        return view('audit_reports.create', compact('clients', 'auditors', 'documentTypes'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        // Build file validation — semua 10 file wajib
        $fileRules = [];
        foreach (array_keys($this->documentTypes) as $type) {
            $mimes = ($type === 'data_klien')
                ? 'mimes:zip,rar,7z,pdf,doc,docx,xls,xlsx|max:102400'
                : 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:51200';
            $fileRules["files.{$type}"] = "required|file|{$mimes}";
        }

        $request->validate(array_merge([
            'client_id'       => 'required|exists:clients,id',
            'report_number'   => 'required|string|unique:audit_reports,report_number',
            'title'           => 'required|string|max:255',
            'audit_type'      => 'required|in:keuangan,kepatuhan',
            'audit_date'      => 'required|date',
            'findings'        => 'required|string',
            'recommendations' => 'nullable|string',
            'auditors'        => 'required|array',
        ], $fileRules));

        // Simpan laporan
        $report = AuditReport::create([
            'client_id'       => $request->client_id,
            'report_number'   => $request->report_number,
            'title'           => $request->title,
            'audit_type'      => $request->audit_type,
            'audit_date'      => $request->audit_date,
            'findings'        => $request->findings,
            'recommendations' => $request->recommendations,
            'status'          => 'pending',
            'created_by'      => Auth::id(),
        ]);

        $report->auditors()->sync($request->auditors);

        // Upload 10 file
        foreach (array_keys($this->documentTypes) as $type) {
            if ($request->hasFile("files.{$type}")) {
                $file = $request->file("files.{$type}");
                $path = $file->store("audit_reports/{$report->id}", 'public');

                AuditReportFile::create([
                    'audit_report_id' => $report->id,
                    'document_name'   => $file->getClientOriginalName(),
                    'document_type'   => $type,
                    'file'            => $path,
                    'uploaded_by'     => Auth::id(),
                ]);
            }
        }

        return redirect()
            ->route('audit_reports.index')
            ->with('success', 'Laporan audit berhasil dibuat');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(AuditReport $auditReport)
    {
        if ($auditReport->status === 'approve') {
            return back()->with('error', 'Laporan yang sudah di-approve tidak dapat diedit');
        }

        $clients       = Client::where('status', 'aktif')->get();
        $auditors      = User::where('role', 'auditor')->get();
        $documentTypes = $this->documentTypes;
        $filesByType   = $auditReport->files->keyBy('document_type');

        return view('audit_reports.edit', compact(
            'auditReport',
            'clients',
            'auditors',
            'documentTypes',
            'filesByType'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, AuditReport $auditReport)
    {
        if ($auditReport->status === 'approve') {
            return back()->with('error', 'Laporan yang sudah di-approve tidak dapat diedit');
        }

        // File rules: required hanya jika belum ada file untuk tipe itu
        $fileRules = [];
        $existingFiles = $auditReport->files->keyBy('document_type');
        foreach (array_keys($this->documentTypes) as $type) {
            $required = isset($existingFiles[$type]) ? 'nullable' : 'required';
            $mimes    = ($type === 'data_klien')
                ? 'mimes:zip,rar,7z,pdf,doc,docx,xls,xlsx|max:102400'
                : 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:51200';
            $fileRules["files.{$type}"] = "{$required}|file|{$mimes}";
        }

        $request->validate(array_merge([
            'client_id'       => 'required|exists:clients,id',
            'title'           => 'required|string|max:255',
            'audit_type'      => 'required|in:keuangan,kepatuhan',
            'audit_date'      => 'required|date',
            'findings'        => 'required|string',
            'recommendations' => 'nullable|string',
            'auditors'        => 'required|array',
        ], $fileRules));

        $auditReport->update([
            'client_id'       => $request->client_id,
            'title'           => $request->title,
            'audit_type'      => $request->audit_type,
            'audit_date'      => $request->audit_date,
            'findings'        => $request->findings,
            'recommendations' => $request->recommendations,
        ]);

        $auditReport->auditors()->sync($request->auditors);

        // Replace file jika ada yang baru diupload
        foreach (array_keys($this->documentTypes) as $type) {
            if ($request->hasFile("files.{$type}")) {
                $file = $request->file("files.{$type}");

                // Hapus file lama
                $existing = $existingFiles[$type] ?? null;
                if ($existing) {
                    Storage::disk('public')->delete($existing->file);
                    $existing->delete();
                }

                // Simpan file baru
                $path = $file->store("audit_reports/{$auditReport->id}", 'public');
                AuditReportFile::create([
                    'audit_report_id' => $auditReport->id,
                    'document_name'   => $file->getClientOriginalName(),
                    'document_type'   => $type,
                    'file'            => $path,
                    'uploaded_by'     => Auth::id(),
                ]);
            }
        }

        // Reset ke pending jika sebelumnya revisi agar pimpinan bisa review ulang
        if ($auditReport->fresh()->status === 'revisi') {
            $auditReport->update([
                'status'        => 'pending',
                'revision_note' => null,
            ]);
        }

        return redirect()
            ->route('audit_reports.index')
            ->with('success', 'Laporan audit berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(AuditReport $auditReport)
    {
        if ($auditReport->status === 'approve') {
            return back()->with('error', 'Laporan yang sudah di-approve tidak dapat dihapus');
        }

        // Hapus semua file dari storage
        foreach ($auditReport->files as $file) {
            Storage::disk('public')->delete($file->file);
        }

        $auditReport->delete();

        return redirect()
            ->route('audit_reports.index')
            ->with('success', 'Laporan audit berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE — Hanya Pimpinan
    |--------------------------------------------------------------------------
    */
    public function approve(int $id)
    {
        $report = AuditReport::with('files')->findOrFail($id);

        $report->update(['status' => 'approve']);

        // Auto-archive semua file ke document_managements
        foreach ($report->files as $file) {
            DocumentManagement::updateOrCreate(
                [
                    'audit_report_id' => $report->id,
                    'document_type'   => $file->document_type,
                ],
                [
                    'document_name' => $file->document_name,
                    'file'          => $file->file,
                    'archived_by'   => Auth::id(),
                    'archived_at'   => now(),
                ]
            );
        }

        return back()->with('success', 'Laporan berhasil di-approve dan dokumen tersimpan ke Manajemen Dokumen');
    }

    /*
    |--------------------------------------------------------------------------
    | REVISI — Hanya Pimpinan
    |--------------------------------------------------------------------------
    */
    public function revisi(Request $request, int $id)
    {
        $request->validate([
            'revision_note' => 'required|string|min:10',
        ], [
            'revision_note.required' => 'Catatan revisi wajib diisi',
            'revision_note.min'      => 'Catatan revisi minimal 10 karakter',
        ]);

        $report = AuditReport::findOrFail($id);

        $report->update([
            'status'        => 'revisi',
            'revision_note' => $request->revision_note,
        ]);

        return back()->with('success', 'Laporan dikembalikan untuk revisi');
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD file individual
    |--------------------------------------------------------------------------
    */
    public function download(int $id)
    {
        $file = AuditReportFile::findOrFail($id);

        return Storage::download(
            'public/' . $file->file,
            $file->document_name
        );
    }
}
