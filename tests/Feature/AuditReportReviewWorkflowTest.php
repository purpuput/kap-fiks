<?php

use App\Models\AuditReport;
use App\Models\AuditReportFile;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->seed(DatabaseSeeder::class);
    
    // Siapkan laporan dengan status 'review' dan file dengan status 'pending_review'
    $report = AuditReport::first();
    $report->update(['status' => 'review']);
    foreach ($report->files as $file) {
        $file->review()->updateOrCreate([
            'audit_report_id' => $report->id,
            'audit_file_id' => $file->id,
        ], [
            'reviewer_id' => null,
            'status' => 'pending_review',
            'note' => null,
            'reviewed_at' => null,
            'is_viewed' => true,
        ]);
        $file->update([
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }
});

test('pimpinan can review audit report files and update overall report status', function () {
    $pimpinan = User::where('role', 'pimpinan')->first();
    
    // Cari laporan dengan status 'review'
    $report = AuditReport::where('status', 'review')->first();
    expect($report)->not->toBeNull();
    
    // Cari salah satu file dari laporan ini
    $file = $report->files()->first();
    expect($file)->not->toBeNull();

    // 1. Pimpinan mereview file sebagai revision (perlu revisi)
    $response = $this->actingAs($pimpinan)
        ->patch(route('audit_reports.files.review', $file), [
            'status' => 'revision',
            'note' => 'Perlu revisi pada bagian ini',
        ]);
        
    $response->assertRedirect();
    
    // Pastikan review dokumen terupdate
    $review = $file->fresh()->review;
    expect($review->status)->toBe('revision');
    expect($review->note)->toBe('Perlu revisi pada bagian ini');
    
    // Pastikan status laporan berubah menjadi 'revisi'
    $report->refresh();
    expect($report->status)->toBe('revisi');

    // 2. Pimpinan mereview file sebagai approved (disetujui)
    $response = $this->actingAs($pimpinan)
        ->patch(route('audit_reports.files.review', $file), [
            'status' => 'approved',
        ]);
        
    $response->assertRedirect();
    
    // Pastikan review dokumen terupdate menjadi approved
    $review = $file->fresh()->review;
    expect($review->status)->toBe('approved');
    expect($review->note)->toBeNull();
});

test('auditor is restricted from uploading to approved files but can upload to revision files and resubmit', function () {
    $pimpinan = User::where('role', 'pimpinan')->first();
    $report = AuditReport::where('status', 'review')->first();
    
    // Dapatkan tipe dokumen yang diperbolehkan untuk auditor
    $controller = new \App\Http\Controllers\AuditReportController();
    $allowedTypesForAuditor = array_keys($controller->getDocumentTypesForRole('auditor'));
    
    // Dapatkan file-file laporan yang hanya bisa diunggah oleh auditor
    $auditorFiles = $report->files()->whereIn('document_type', $allowedTypesForAuditor)->get();
    expect($auditorFiles->count())->toBeGreaterThanOrEqual(2);
    
    $fileApproved = $auditorFiles[0];
    $fileRevision = $auditorFiles[1];
    
    // Pimpinan menyetujui fileApproved dan meminta revisi fileRevision
    $this->actingAs($pimpinan)->patch(route('audit_reports.files.review', $fileApproved), ['status' => 'approved']);
    $this->actingAs($pimpinan)->patch(route('audit_reports.files.review', $fileRevision), ['status' => 'revision', 'note' => 'Tolong revisi']);
    
    $report->refresh();
    expect($report->status)->toBe('revisi');
    
    // Auditor yang ditugaskan ke laporan ini mencoba mengunggah
    $auditor = User::find($report->created_by);
    
    // Mengunggah file yang sudah disetujui harus memicu error validasi
    $response = $this->actingAs($auditor)
        ->put(route('audit_reports.update', $report), [
            'audit_date' => now()->toDateString(),
            'files' => [
                $fileApproved->document_type => UploadedFile::fake()->create('dummy.pdf', 100),
            ]
        ]);
        
    $response->assertSessionHasErrors('files.' . $fileApproved->document_type);
    
    // Mengunggah file yang perlu revisi harus berhasil
    $response = $this->actingAs($auditor)
        ->put(route('audit_reports.update', $report), [
            'audit_date' => now()->toDateString(),
            'files' => [
                $fileRevision->document_type => UploadedFile::fake()->create('revised.pdf', 100),
            ]
        ]);
        
    $response->assertRedirect(route('audit_reports.index'));
    
    // Pastikan file terupdate dengan versi baru dan review diset ulang ke pending_review
    $newFile = $report->files()->where('document_type', $fileRevision->document_type)->first();
    expect($newFile->version)->toBeGreaterThan($fileRevision->version);
    expect($newFile->review->status)->toBe('pending_review');
    
    // Auditor mengirim kembali laporan
    $response = $this->actingAs($auditor)
        ->put(route('audit_reports.resubmit', $report));
        
    $response->assertRedirect();
    
    $report->refresh();
    expect($report->status)->toBe('review');
});

test('pimpinan approving all files automatically marks report as disetujui', function () {
    $pimpinan = User::where('role', 'pimpinan')->first();
    $report = AuditReport::where('status', 'review')->first();
    
    // Pimpinan menyetujui semua file laporan
    foreach ($report->files as $file) {
        $this->actingAs($pimpinan)->patch(route('audit_reports.files.review', $file), [
            'status' => 'approved',
        ]);
    }
    
    $report->refresh();
    expect($report->status)->toBe('disetujui');
});

test('pimpinan must view or download document before reviewing it', function () {
    $pimpinan = User::where('role', 'pimpinan')->first();
    $report = AuditReport::where('status', 'review')->first();
    $file = $report->files()->first();
    
    // Set is_viewed to false
    $file->review()->update(['is_viewed' => false]);
    
    // 1. Mencoba review tanpa melihat dokumen terlebih dahulu harus gagal
    $response = $this->actingAs($pimpinan)
        ->from(route('audit_reports.show', $report))
        ->patch(route('audit_reports.files.review', $file), [
            'status' => 'approved',
        ]);
        
    $response->assertRedirect(route('audit_reports.show', $report));
    $response->assertSessionHas('error', 'Dokumen harus dilihat atau diunduh terlebih dahulu sebelum direview.');
    
    // 2. Lakukan preview/view pada file
    $responsePreview = $this->actingAs($pimpinan)
        ->get(route('audit_reports.preview', $file));
        
    // 3. Pastikan is_viewed berubah menjadi true
    expect($file->fresh()->review->is_viewed)->toBeTrue();
    
    // 4. Coba review kembali setelah melihat dokumen harus berhasil
    $responseSuccess = $this->actingAs($pimpinan)
        ->patch(route('audit_reports.files.review', $file), [
            'status' => 'approved',
        ]);
        
    $responseSuccess->assertRedirect();
    expect($file->fresh()->review->status)->toBe('approved');
});
