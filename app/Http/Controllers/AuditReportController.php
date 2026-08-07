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
            'files.review'
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

        // Hanya klien yang sudah disetujui pimpinan dan belum memiliki laporan audit
        $clients = Client::where('approval_status', 'disetujui')
            ->where('status', 'aktif')
            ->whereDoesntHave('auditReports')
            ->with('auditors')
            ->get();

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

                ? 'mimes:zip,rar,tar,pdf|max:102400'

                : 'mimes:pdf,doc,docx,xls,xlsx|max:51200';

            $fileRules["files.{$type}"] =
                "required|file|{$mimes}";
        }

        $messages = [
            'client_id.required' => 'Klien wajib dipilih.',
            'client_id.exists' => 'Klien yang Anda pilih tidak valid.',
            'audit_date.required' => 'Tanggal Pembuatan Laporan wajib diisi.',
            'audit_date.date' => 'Format Tanggal Pembuatan Laporan tidak valid.',
        ];

        foreach (array_keys($allowedTypes) as $type) {
            $label = $allowedTypes[$type] ?? $type;
            $messages["files.{$type}.required"] = "Dokumen {$label} wajib diunggah.";
            $messages["files.{$type}.file"] = "Dokumen {$label} harus berupa berkas/file yang valid.";
            $messages["files.{$type}.mimes"] = ($type === 'data_klien')
                ? "Format dokumen {$label} harus berupa ZIP, RAR, TAR, atau PDF."
                : "Format dokumen {$label} harus berupa PDF, DOC, DOCX, XLS, atau XLSX.";
            $messages["files.{$type}.max"] = ($type === 'data_klien')
                ? "Ukuran dokumen {$label} tidak boleh melebihi 100MB."
                : "Ukuran dokumen {$label} tidak boleh melebihi 50MB.";
        }

        // Validasi form
        $request->validate(array_merge([
            'client_id' => 'required|exists:clients,id',
            'audit_date' => 'required|date',
        ], $fileRules), $messages);

        // Client (pastikan disetujui pimpinan dan belum memiliki laporan audit)
        $client = Client::where('id', $request->client_id)
            ->where('approval_status', 'disetujui')
            ->where('status', 'aktif')
            ->whereDoesntHave('auditReports')
            ->first();

        if (!$client) {
            return back()->withErrors(['client_id' => 'Klien tidak ditemukan, tidak aktif, belum disetujui oleh pimpinan, atau sudah memiliki laporan audit.'])->withInput();
        }

        // Generate nomor laporan
        $lastReport = AuditReport::latest('id')->first();

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

        // Kirim notifikasi pembuatan laporan audit ke Pimpinan & Auditor tim
        try {
            $recipients = collect();
            $pimpinans = \App\Models\User::where('role', 'pimpinan')->get();
            $recipients = $recipients->merge($pimpinans);

            if ($client->auditors) {
                $recipients = $recipients->merge($client->auditors);
            }
            $recipients = $recipients->unique('id');

            foreach ($recipients as $recipient) {
                if ($recipient->id !== Auth::id()) {
                    $recipient->notify(new \App\Notifications\AuditReportReviewedNotification(
                        $report,
                        'created',
                        null,
                        null,
                        Auth::user()->name
                    ));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi pembuatan laporan audit: ' . $e->getMessage());
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

        // Hanya klien yang sudah disetujui pimpinan (dan belum memiliki laporan audit, kecuali klien laporan ini sendiri)
        $clients = Client::where('approval_status', 'disetujui')
            ->where('status', 'aktif')
            ->where(function ($query) use ($auditReport) {
                $query->whereDoesntHave('auditReports')
                      ->orWhere('id', $auditReport->client_id);
            })
            ->with('auditors')
            ->get();

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
        $existingFiles = $auditReport->files->keyBy('document_type');
        $allowedTypes = $this->getDocumentTypesForRole(Auth::user()->role);

        // Validasi input: auditor tidak boleh mengunggah file yang sudah disetujui
        if (Auth::user()->role === 'auditor') {
            foreach ($request->file('files') ?? [] as $type => $file) {
                $existing = $existingFiles[$type] ?? null;
                if ($existing) {
                    $review = $existing->review;
                    if ($review && $review->status === 'approved') {
                        return back()->withErrors(["files.{$type}" => "Dokumen " . ($this->documentTypes[$type] ?? $type) . " sudah disetujui dan tidak dapat diubah."]);
                    }
                }
            }
        }

        // Validasi file
        foreach (array_keys($allowedTypes) as $type) {
            $existing = $existingFiles[$type] ?? null;
            if ($existing && Auth::user()->role === 'auditor') {
                $review = $existing->review;
                if ($review && $review->status === 'approved') {
                    // Dokumen yang sudah disetujui dilewati dari validasi file baru
                    continue;
                }
            }

            $required = isset($existingFiles[$type])
                ? 'nullable'
                : 'required';

            $mimes = ($type === 'data_klien')
                ? 'mimes:zip,rar,7z,tar,pdf,doc,docx,xls,xlsx|max:102400'
                : 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:51200';

            $fileRules["files.{$type}"] =
                "{$required}|file|{$mimes}";
        }

        $messages = [
            'client_id.required' => 'Klien wajib dipilih.',
            'client_id.exists' => 'Klien yang Anda pilih tidak valid.',
            'audit_date.required' => 'Tanggal Pembuatan Laporan wajib diisi.',
            'audit_date.date' => 'Format Tanggal Pembuatan Laporan tidak valid.',
        ];

        foreach (array_keys($allowedTypes) as $type) {
            $label = $this->documentTypes[$type] ?? $type;
            $messages["files.{$type}.required"] = "Dokumen {$label} wajib diunggah.";
            $messages["files.{$type}.file"] = "Dokumen {$label} harus berupa berkas/file yang valid.";
            $messages["files.{$type}.mimes"] = ($type === 'data_klien')
                ? "Format dokumen {$label} harus berupa ZIP, RAR, 7Z, TAR, PDF, Word, atau Excel."
                : "Format dokumen {$label} harus berupa PDF, Word (DOC/DOCX), Excel (XLS/XLSX), atau Gambar (JPG/JPEG/PNG).";
            $messages["files.{$type}.max"] = ($type === 'data_klien')
                ? "Ukuran dokumen {$label} tidak boleh melebihi 100MB."
                : "Ukuran dokumen {$label} tidak boleh melebihi 50MB.";
        }

        // Validasi form (Client id dan audit date hanya bisa diubah admin)
        $validationRules = [
            'audit_date' => 'required|date',
        ];
        if (Auth::user()->role === 'admin') {
            $validationRules['client_id'] = 'required|exists:clients,id';
        }
        $request->validate(array_merge($validationRules, $fileRules), $messages);

        // Update laporan
        $updateData = ['audit_date' => $request->audit_date];
        if (Auth::user()->role === 'admin') {
            $client = Client::where('id', $request->client_id)
                ->where('approval_status', 'disetujui')
                ->where('status', 'aktif')
                ->where(function ($query) use ($auditReport) {
                    $query->whereDoesntHave('auditReports')
                          ->orWhere('id', $auditReport->client_id);
                })
                ->first();

            if (!$client) {
                return back()->withErrors(['client_id' => 'Klien tidak ditemukan, tidak aktif, belum disetujui oleh pimpinan, atau sudah memiliki laporan audit.'])->withInput();
            }
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

                // Hapus file lama beserta reviewnya (karena cascade delete)
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

                $newFile = AuditReportFile::create([
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

                // Buat/perbarui review menjadi pending_review
                \App\Models\DocumentReview::updateOrCreate(
                    [
                        'audit_report_id' => $auditReport->id,
                        'audit_file_id' => $newFile->id,
                    ],
                    [
                        'reviewer_id' => null,
                        'status' => 'pending_review',
                        'note' => null,
                        'reviewed_at' => null,
                    ]
                );
            }
        }

        // Perbarui status laporan secara otomatis berdasarkan status review berkas (kembali ke 'review' jika file revisi diperbarui)
        $auditReport->updateStatusFromReviews();

        // Kirim notifikasi pembaruan laporan ke Pimpinan & pihak terkait
        try {
            $recipients = collect();
            $pimpinans = \App\Models\User::where('role', 'pimpinan')->get();
            $recipients = $recipients->merge($pimpinans);

            if ($auditReport->creator) {
                $recipients->push($auditReport->creator);
            }
            if ($auditReport->client && $auditReport->client->auditors) {
                $recipients = $recipients->merge($auditReport->client->auditors);
            }
            $recipients = $recipients->unique('id');

            foreach ($recipients as $recipient) {
                if ($recipient->id !== Auth::id()) {
                    $recipient->notify(new \App\Notifications\AuditReportReviewedNotification(
                        $auditReport,
                        'updated',
                        null,
                        null,
                        Auth::user()->name
                    ));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi update laporan audit: ' . $e->getMessage());
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
     * Review file (dokumen audit per item)
     */
    public function reviewFile(Request $request, AuditReportFile $file)
    {
        // Hanya pimpinan
        abort_if(Auth::user()->role !== 'pimpinan', 403);

        // Pastikan dokumen telah dilihat/diunduh terlebih dahulu
        $review = \App\Models\DocumentReview::where('audit_file_id', $file->id)->first();
        if (!$review || !$review->is_viewed) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen harus dilihat atau diunduh terlebih dahulu sebelum direview.'
                ], 422);
            }
            return back()->with('error', 'Dokumen harus dilihat atau diunduh terlebih dahulu sebelum direview.');
        }

        $request->validate([
            'status' => 'required|in:approved,revision',
            'note' => 'required_if:status,revision|nullable|string',
        ]);

        // Simpan review dokumen
        \App\Models\DocumentReview::updateOrCreate(
            [
                'audit_report_id' => $file->audit_report_id,
                'audit_file_id' => $file->id,
            ],
            [
                'reviewer_id' => Auth::id(),
                'status' => $request->status,
                'note' => $request->status === 'revision' ? $request->note : null,
                'reviewed_at' => now(),
            ]
        );

        // Update status laporan secara otomatis
        $auditReport = $file->auditReport;
        $auditReport->updateStatusFromReviews();

        // Update file status untuk backward compatibility
        $file->update([
            'status' => $request->status === 'approved' ? 'lengkap' : 'belum lengkap',
            'revision_note' => $request->status === 'revision' ? $request->note : null,
            'approved_by' => Auth::id(),
            'approved_at' => $request->status === 'approved' ? now() : null,
        ]);

        // Kirim notifikasi ke auditor tim dan admin pembuat laporan
        try {
            $recipients = collect();
            if ($auditReport->creator) {
                $recipients->push($auditReport->creator);
            }
            if ($auditReport->client && $auditReport->client->auditors) {
                $recipients = $recipients->merge($auditReport->client->auditors);
            }
            $recipients = $recipients->unique('id');

            foreach ($recipients as $recipient) {
                if ($recipient->id !== Auth::id()) {
                    $recipient->notify(new \App\Notifications\AuditReportReviewedNotification(
                        $auditReport,
                        $request->status,
                        $file,
                        $request->status === 'revision' ? $request->note : null,
                        Auth::user()->name
                    ));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi review file: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review dokumen berhasil disimpan.',
                'review_status' => $request->status,
                'review_note' => $request->status === 'revision' ? $request->note : null,
                'report_status' => $auditReport->fresh()->status,
            ]);
        }

        return back()->with('success', 'Review dokumen berhasil disimpan.');
    }

    /**
     * Kirim ulang review (Auditor mengirim kembali setelah perbaikan)
     */
    public function resubmit(AuditReport $auditReport)
    {
        // Hanya auditor yang ditugaskan atau admin
        if (Auth::user()->role === 'auditor') {
            abort_if(!$auditReport->client->auditors()->where('users.id', Auth::id())->exists(), 403);
        }

        // Pastikan tidak ada dokumen yang statusnya 'revision'
        $hasRevisions = $auditReport->documentReviews()->where('status', 'revision')->exists();
        if ($hasRevisions) {
            return back()->with('error', 'Masih ada dokumen yang belum diperbaiki.');
        }

        // Ubah status laporan ke review
        $auditReport->updateStatusFromReviews(true);

        // Kirim notifikasi ke pimpinan
        try {
            $pimpinans = \App\Models\User::where('role', 'pimpinan')->get();
            foreach ($pimpinans as $pimpinan) {
                $pimpinan->notify(new \App\Notifications\AuditReportReviewedNotification(
                    $auditReport,
                    'resubmitted',
                    null,
                    null,
                    Auth::user()->name
                ));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi resubmit: ' . $e->getMessage());
        }

        return back()->with('success', 'Laporan berhasil dikirim ulang untuk direview.');
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

        if (Auth::user()->role === 'pimpinan') {
            \App\Models\DocumentReview::updateOrCreate(
                [
                    'audit_report_id' => $file->audit_report_id,
                    'audit_file_id' => $file->id,
                ],
                [
                    'is_viewed' => true,
                ]
            );
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

        if (Auth::user()->role === 'pimpinan') {
            \App\Models\DocumentReview::updateOrCreate(
                [
                    'audit_report_id' => $file->audit_report_id,
                    'audit_file_id' => $file->id,
                ],
                [
                    'is_viewed' => true,
                ]
            );
        }

        $path = Storage::disk('local')->path($file->file);
        return response()->file($path);
    }
}
