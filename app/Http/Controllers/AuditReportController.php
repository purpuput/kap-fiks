<?php

namespace App\Http\Controllers;

use App\Models\AuditReport;
use App\Models\AuditReportFile;
use App\Models\Client;

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
     * Tipe dokumen berdasarkan role
     */
    public function getDocumentTypesForRole(string $role): array
    {
        if ($role === 'admin') {
            return [
                'spk' => 'SPK',
                'surat_tugas' => 'Surat Tugas',
                'invoice' => 'Invoice',
            ];
        } elseif ($role === 'auditor') {
            return [
                'laporan_keuangan' => 'Laporan Keuangan',
                'kertas_kerja' => 'Kertas Kerja',
                'berita_acara_pemeriksaan' => 'Berita Acara Pemeriksaan',
                'data_klien' => 'Data Klien',
                'surat_pernyataan_klien' => 'Surat Pernyataan Klien',
                'management_letter' => 'Management Letter',
                'laporan_audit' => 'Laporan Audit',
            ];
        }
        return $this->documentTypes;
    }

    /**
     * List laporan
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $audit_type = $request->audit_type;

        $query = AuditReport::with([
            'client.auditors',
            'creator',
            'reviewer',
            'files'
        ]);

        // Auditor hanya melihat laporan untuk klien yang diassign kepadanya dan sudah disetujui
        if (Auth::user()->role === 'auditor') {
            $query->whereHas('client.auditors', function ($q) {
                $q->where('users.id', Auth::id());
            })->whereHas('client', function ($q) {
                $q->where('approval_status', 'disetujui');
            });
        }

        // Hitung statistik berdasarkan role (sebelum filter pencarian & status diaplikasikan)
        $statsQuery = AuditReport::query();
        if (Auth::user()->role === 'auditor') {
            $statsQuery->whereHas('client.auditors', function ($q) {
                $q->where('users.id', Auth::id());
            })->whereHas('client', function ($q) {
                $q->where('approval_status', 'disetujui');
            });
        }

        $totalReports = (clone $statsQuery)->count();
        $disetujuiCount = (clone $statsQuery)->where('status', 'disetujui')->count();
        $reviewCount = (clone $statsQuery)->where('status', 'review')->count();
        $revisiCount = (clone $statsQuery)->where('status', 'revisi')->count();

        // Search berdasarkan nomor laporan, judul, klien, auditor
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('report_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('institution_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('client.auditors', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter status
        if ($status) {
            $query->where('status', $status);
        }

        // Filter jenis audit
        if ($audit_type) {
            $query->where('audit_type', $audit_type);
        }

        $reports = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'audit_reports.index',
            compact(
                'reports',
                'search',
                'status',
                'audit_type',
                'totalReports',
                'disetujuiCount',
                'reviewCount',
                'revisiCount'
            )
        );
    }

    /**
     * Detail laporan
     */
    public function show(AuditReport $auditReport)
    {
        // Auditor hanya boleh melihat laporan jika diassign ke klien tersebut
        if (
            Auth::user()->role === 'auditor' &&
            !$auditReport->client->auditors()->where('users.id', Auth::id())->exists()
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
        // Hanya admin
        abort_if(
            Auth::user()->role !== 'admin',
            403
        );

        // Semua klien
        $clients = Client::with('auditors')->get();

        $documentTypes = $this->getDocumentTypesForRole('admin');

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
        // Hanya admin
        abort_if(
            Auth::user()->role !== 'admin',
            403
        );

        $fileRules = [];
        $allowedTypes = $this->getDocumentTypesForRole('admin');

        // Validasi file
        foreach (array_keys($allowedTypes) as $type) {

            $mimes = ($type === 'data_klien')

                ? 'mimes:pdf,zip,tar|max:102400'

                : 'mimes:pdf,zip,tar|max:51200';

            $fileRules["files.{$type}"] =
                "required|file|{$mimes}";
        }

        // Validasi form
        $request->validate(array_merge([
            'client_id' => 'required|exists:clients,id',
            'audit_date' => 'required|date',
        ], $fileRules));

        // Client
        $client = Client::findOrFail($request->client_id);

        // Generate nomor laporan
        $lastReport = AuditReport::latest()->first();

        $number = $lastReport
            ? ((int) explode('/', $lastReport->report_number)[0]) + 1
            : 1;

        $formattedNumber = str_pad($number, 2, '0', STR_PAD_LEFT);
        $auditType = $client->audit_type === 'keuangan' ? 'KEU' : 'KPT';
        $month = date('m');
        $year = date('Y');

        $reportNumber = "{$formattedNumber}/LA/{$auditType}/{$month}/{$year}";

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
        foreach (array_keys($allowedTypes) as $type) {

            if (
                $request->hasFile("files.{$type}")
            ) {

                $file = $request->file(
                    "files.{$type}"
                );

                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs(

                    "audit_reports/{$report->id}",

                    $filename,

                    'local'
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
        // Hanya admin dan auditor
        abort_if(!in_array(Auth::user()->role, ['admin', 'auditor']), 403);

        // Jika auditor, pastikan di-assign ke klien
        if (Auth::user()->role === 'auditor') {
            abort_if(!$auditReport->client->auditors()->where('users.id', Auth::id())->exists(), 403);
        }

        // Final tidak bisa edit
        if (
            $auditReport->status === 'disetujui'
        ) {

            return back()->with(

                'error',

                'Laporan tidak dapat diedit.'
            );
        }

        $clients = Client::with('auditors')->get();

        $documentTypes = $this->documentTypes;
        $allowedDocumentTypes = $this->getDocumentTypesForRole(Auth::user()->role);

        $filesByType = $auditReport->files
            ->keyBy('document_type');

        return view(
            'audit_reports.edit',
            compact(

                'auditReport',

                'clients',
                'documentTypes',
                'allowedDocumentTypes',
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
        // Hanya admin dan auditor
        abort_if(!in_array(Auth::user()->role, ['admin', 'auditor']), 403);

        // Jika auditor, pastikan di-assign ke klien
        if (Auth::user()->role === 'auditor') {
            abort_if(!$auditReport->client->auditors()->where('users.id', Auth::id())->exists(), 403);
        }

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

        $allowedTypes = $this->getDocumentTypesForRole(Auth::user()->role);

        // Validasi file
        foreach (array_keys($allowedTypes) as $type) {

            $required = isset(
                $existingFiles[$type]
            )
                ? 'nullable'
                : 'required';

            $mimes = ($type === 'data_klien')

                ? 'mimes:pdf,zip,tar|max:102400'

                : 'mimes:pdf,zip,tar|max:51200';

            $fileRules["files.{$type}"] =
                "{$required}|file|{$mimes}";
        }

        // Validasi form (Client id dan audit date hanya bisa diubah admin)
        $validationRules = [
            'audit_date' => 'required|date',
        ];
        if (Auth::user()->role === 'admin') {
            $validationRules['client_id'] = 'required|exists:clients,id';
        }
        $request->validate(array_merge($validationRules, $fileRules));

        // Update laporan
        $updateData = ['audit_date' => $request->audit_date];
        if (Auth::user()->role === 'admin') {
            $client = Client::findOrFail($request->client_id);
            $updateData['client_id'] = $request->client_id;
            $updateData['title'] = 'Laporan Audit - ' . $client->institution_name;
            $updateData['audit_type'] = $client->audit_type;
        }

        $auditReport->update($updateData);

        // Update file
        foreach (array_keys($allowedTypes) as $type) {

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

                    Storage::disk('local')
                        ->delete($existing->file);

                    $existing->delete();

                }

                // Upload file baru
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs(

                    "audit_reports/{$auditReport->id}",

                    $filename,

                    'local'
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
        // Hanya admin yang bisa hapus
        abort_if(Auth::user()->role !== 'admin', 403);

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
        // Pengecekan otorisasi auditor
        if (Auth::user()->role === 'auditor') {
            $hasAccess = $file->auditReport->client->auditors()->where('users.id', Auth::id())->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk dokumen ini.');
            }
        }

        if (empty($file->file) || !Storage::disk('local')->exists($file->file)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return Storage::disk('local')->download($file->file, $file->document_name);
    }

    /**
     * Preview file
     */
    public function preview(AuditReportFile $file)
    {
        // Pengecekan otorisasi auditor
        if (Auth::user()->role === 'auditor') {
            $hasAccess = $file->auditReport->client->auditors()->where('users.id', Auth::id())->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk dokumen ini.');
            }
        }

        if (empty($file->file) || !Storage::disk('local')->exists($file->file)) {
            return response('<div style="text-align:center; padding: 50px; font-family: sans-serif; color: #666;"><h3>File Tidak Ditemukan</h3><p>File fisik tidak ada di server karena ini adalah data dummy atau file telah dihapus.</p></div>', 404);
        }

        $path = Storage::disk('local')->path($file->file);
        return response()->file($path);
    }
}
