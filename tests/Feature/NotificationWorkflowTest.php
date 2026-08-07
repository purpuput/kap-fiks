<?php

use App\Models\AuditReport;
use App\Models\AuditReportFile;
use App\Models\Client;
use App\Models\Logbook;
use App\Models\User;
use App\Notifications\LogbookReviewedNotification;
use App\Notifications\AuditReportReviewedNotification;
use Illuminate\Support\Facades\Notification;

test('logbook review sends notification to owner', function () {
    Notification::fake();

    $auditor = User::factory()->create(['role' => 'auditor']);
    $pimpinan = User::factory()->create(['role' => 'pimpinan']);

    $logbook = Logbook::create([
        'user_id' => $auditor->id,
        'date' => now()->toDateString(),
        'main_task' => 'Melakukan pengujian pengeluaran kas',
        'has_obstacle' => false,
        'next_plan' => 'Melanjutkan pengujian sampel',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($pimpinan)
        ->from(route('logbooks.index'))
        ->patch(route('logbooks.approve', $logbook->id));

    $response->assertSessionHas('success');

    Notification::assertSentTo(
        [$auditor],
        LogbookReviewedNotification::class,
        function ($notification) {
            return $notification->actionStatus === 'approved';
        }
    );
});

test('audit report review sends notification to team', function () {
    Notification::fake();

    $pimpinan = User::factory()->create(['role' => 'pimpinan']);
    $auditor = User::factory()->create(['role' => 'auditor']);
    $admin = User::factory()->create(['role' => 'admin']);

    $client = Client::create([
        'institution_type' => 'Swasta',
        'audit_type' => 'keuangan',
        'institution_name' => 'PT Test Notifikasi',
        'leader_name' => 'Budi Mandiri',
        'address' => 'Jl. Test No 1',
        'city' => 'Semarang',
        'province' => 'Jawa Tengah',
        'postal_code' => '50123',
        'phone' => '081234567890',
        'email' => 'client@test.com',
        'npwp' => '12.345.678.9-123.000',
        'approval_status' => 'disetujui',
        'status' => 'aktif',
        'created_by' => $admin->id,
    ]);

    $client->auditors()->attach($auditor->id, ['team_role' => 'ketua']);

    $auditReport = AuditReport::create([
        'client_id' => $client->id,
        'report_number' => '01/LA/KEU/07/2026',
        'title' => 'Laporan Audit PT Test Notifikasi',
        'audit_type' => 'keuangan',
        'audit_date' => now()->toDateString(),
        'findings' => 'Catatan temuan',
        'recommendations' => 'Rekomendasi audit',
        'status' => 'review',
        'created_by' => $admin->id,
    ]);

    $file = AuditReportFile::create([
        'audit_report_id' => $auditReport->id,
        'document_name' => 'laporan_keuangan.pdf',
        'document_type' => 'laporan_keuangan',
        'file' => 'audit_reports/test/laporan_keuangan.pdf',
        'version' => 1,
        'is_active' => true,
        'uploaded_by' => $auditor->id,
    ]);

    \App\Models\DocumentReview::create([
        'audit_report_id' => $auditReport->id,
        'audit_file_id' => $file->id,
        'is_viewed' => true,
    ]);

    $response = $this->actingAs($pimpinan)
        ->from(route('audit_reports.show', $auditReport))
        ->patch(route('audit_reports.files.review', $file->id), [
            'status' => 'approved',
        ]);

    $response->assertSessionHas('success');

    Notification::assertSentTo(
        [$auditor, $admin],
        AuditReportReviewedNotification::class
    );
});

test('notification unread data endpoint returns json structure', function () {
    $user = User::factory()->create(['role' => 'auditor']);

    $user->notify(new LogbookReviewedNotification(
        Logbook::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'main_task' => 'Test Task',
            'has_obstacle' => false,
            'next_plan' => 'Test Next',
            'status' => 'approved',
        ]),
        'approved',
        'Pimpinan Test'
    ));

    $response = $this->actingAs($user)
        ->get(route('notifications.unread_data'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'unread_count',
        'recent' => [
            '*' => ['id', 'type', 'title', 'message', 'url', 'read', 'created_at', 'icon']
        ]
    ]);
    expect($response->json('unread_count'))->toBe(1);
});
