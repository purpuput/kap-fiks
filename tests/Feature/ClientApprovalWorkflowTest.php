<?php

use App\Models\Client;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->seed(DatabaseSeeder::class);
});

test('admin cannot create an audit report for a pending client', function () {
    $admin = User::where('role', 'admin')->first();
    $clientPending = Client::where('approval_status', 'pending')->first();
    expect($clientPending)->not->toBeNull();

    $response = $this->actingAs($admin)
        ->from(route('audit_reports.create'))
        ->post(route('audit_reports.store'), [
            'client_id' => $clientPending->id,
            'audit_date' => now()->toDateString(),
            'files' => [
                'spk' => UploadedFile::fake()->create('spk.pdf', 100),
                'surat_tugas' => UploadedFile::fake()->create('st.pdf', 100),
                'invoice' => UploadedFile::fake()->create('inv.pdf', 100),
            ]
        ]);

    $response->assertRedirect(route('audit_reports.create'));
    $response->assertSessionHasErrors('client_id');
});

test('admin can create an audit report for an approved client', function () {
    $admin = User::where('role', 'admin')->first();
    $pimpinan = User::where('role', 'pimpinan')->first();

    // Buat klien disetujui baru yang belum punya laporan audit
    $clientApproved = Client::create([
        'created_by' => $admin->id,
        'institution_type' => 'Swasta',
        'audit_type' => 'keuangan',
        'institution_name' => 'PT Test Baru',
        'leader_name' => 'Budi',
        'address' => 'Jl. Test',
        'city' => 'Jambi',
        'province' => 'Jambi',
        'postal_code' => '36111',
        'phone' => '081234567890',
        'email' => 'testbaru@gmail.com',
        'npwp' => '01.234.567.8-999.000',
        'contract_start' => now(),
        'contract_end' => now()->addMonths(6),
        'contract_status' => 'aktif',
        'status' => 'aktif',
        'approval_status' => 'disetujui',
        'approved_by' => $pimpinan->id,
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->post(route('audit_reports.store'), [
            'client_id' => $clientApproved->id,
            'audit_date' => now()->toDateString(),
            'files' => [
                'spk' => UploadedFile::fake()->create('spk.pdf', 100),
                'surat_tugas' => UploadedFile::fake()->create('st.pdf', 100),
                'invoice' => UploadedFile::fake()->create('inv.pdf', 100),
            ]
        ]);

    $response->assertRedirect(route('audit_reports.index'));
    $response->assertSessionHasNoErrors();
});

test('admin cannot create a duplicate audit report for a client that already has one', function () {
    $admin = User::where('role', 'admin')->first();
    // Klien disetujui yang sudah memiliki laporan audit dari seeder
    $clientApprovedWithReport = Client::where('approval_status', 'disetujui')
        ->whereHas('auditReports')
        ->first();
    expect($clientApprovedWithReport)->not->toBeNull();

    $response = $this->actingAs($admin)
        ->from(route('audit_reports.create'))
        ->post(route('audit_reports.store'), [
            'client_id' => $clientApprovedWithReport->id,
            'audit_date' => now()->toDateString(),
            'files' => [
                'spk' => UploadedFile::fake()->create('spk.pdf', 100),
                'surat_tugas' => UploadedFile::fake()->create('st.pdf', 100),
                'invoice' => UploadedFile::fake()->create('inv.pdf', 100),
            ]
        ]);

    $response->assertRedirect(route('audit_reports.create'));
    $response->assertSessionHasErrors('client_id');
});

test('admin cannot edit or update an approved client', function () {
    $admin = User::where('role', 'admin')->first();
    $clientApproved = Client::where('approval_status', 'disetujui')->first();
    expect($clientApproved)->not->toBeNull();

    // 1. Coba akses form edit
    $responseEdit = $this->actingAs($admin)
        ->get(route('clients.edit', $clientApproved));
    $responseEdit->assertRedirect(route('clients.index'));
    $responseEdit->assertSessionHas('error');

    // 2. Coba kirim request update
    $responseUpdate = $this->actingAs($admin)
        ->put(route('clients.update', $clientApproved), [
            'institution_type' => $clientApproved->institution_type,
            'audit_type' => $clientApproved->audit_type,
            'institution_name' => 'Nama Baru',
            'leader_name' => $clientApproved->leader_name,
            'address' => $clientApproved->address,
            'city' => $clientApproved->city,
            'province' => $clientApproved->province,
            'postal_code' => $clientApproved->postal_code,
            'phone' => $clientApproved->phone,
            'email' => $clientApproved->email,
            'npwp' => $clientApproved->npwp,
            'status' => 'aktif',
            'auditors' => $clientApproved->auditors->pluck('id')->toArray(),
            'team_role' => $clientApproved->auditors->mapWithKeys(fn($a) => [$a->id => $a->pivot->team_role])->toArray(),
        ]);

    $responseUpdate->assertRedirect(route('clients.index'));
    $responseUpdate->assertSessionHas('error');

    // Pastikan database tidak terupdate
    expect($clientApproved->fresh()->institution_name)->not->toBe('Nama Baru');
});

test('admin can edit and update a pending client', function () {
    $admin = User::where('role', 'admin')->first();
    $clientPending = Client::where('approval_status', 'pending')->first();
    expect($clientPending)->not->toBeNull();

    // 1. Coba akses form edit
    $responseEdit = $this->actingAs($admin)
        ->get(route('clients.edit', $clientPending));
    $responseEdit->assertOk();

    // 2. Coba kirim request update
    $responseUpdate = $this->actingAs($admin)
        ->put(route('clients.update', $clientPending), [
            'institution_type' => $clientPending->institution_type,
            'audit_type' => $clientPending->audit_type,
            'institution_name' => 'Nama Baru Pending',
            'leader_name' => $clientPending->leader_name,
            'address' => $clientPending->address,
            'city' => $clientPending->city,
            'province' => $clientPending->province,
            'postal_code' => $clientPending->postal_code,
            'phone' => $clientPending->phone,
            'email' => $clientPending->email,
            'npwp' => $clientPending->npwp,
            'status' => 'aktif',
            'auditors' => $clientPending->auditors->pluck('id')->toArray(),
            'team_role' => $clientPending->auditors->mapWithKeys(fn($a) => [$a->id => $a->pivot->team_role])->toArray(),
        ]);

    $responseUpdate->assertRedirect(route('clients.index'));
    $responseUpdate->assertSessionHas('success');

    // Pastikan database terupdate
    expect($clientPending->fresh()->institution_name)->toBe('Nama Baru Pending');
});
