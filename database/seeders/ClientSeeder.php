<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // User role
        $admin = User::query()->where('role', 'admin')->first();

        $pimpinan = User::query()->where('role', 'pimpinan')->first();

        $auditors = User::query()->where('role', 'auditor')->get();

        /*
        |--------------------------------------------------------------------------
        | PT Maju Jaya
        |--------------------------------------------------------------------------
        */
        $client1 = Client::create([

            'created_by' => $admin->id,

            'institution_type' => 'Swasta',

            'audit_type' => 'keuangan',

            'institution_name' => 'PT Maju Jaya',

            'leader_name' => 'Budi Santoso',

            'address' => 'Jl. Sudirman No. 10',

            'city' => 'Kota Jambi',

            'province' => 'Jambi',

            'postal_code' => '36111',

            'phone' => '081234567890',

            'email' => 'majujaya@gmail.com',

            'npwp' => '01.234.567.8-999.000',

            'contract_start' => now(),

            'contract_end' => now()->addMonths(6),

            'contract_status' => 'aktif',

            'status' => 'aktif',

            'approval_status' => 'disetujui',

            'approved_by' => $pimpinan->id,

            'approved_at' => now(),

            'description' => 'Klien audit keuangan.',
        ]);

        // Tim audit
        if ($auditors->count() >= 2) {

            $client1->auditors()->attach(
                $auditors[0]->id,
                ['team_role' => 'ketua']
            );

            $client1->auditors()->attach(
                $auditors[1]->id,
                ['team_role' => 'anggota']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Dinas Pendidikan Kota Jambi
        |--------------------------------------------------------------------------
        */
        $client2 = Client::create([

            'created_by' => $admin->id,

            'institution_type' => 'Pemerintahan',

            'audit_type' => 'kepatuhan',

            'institution_name' => 'Dinas Pendidikan Kota Jambi',

            'leader_name' => 'Ahmad Rizki',

            'address' => 'Jl. Pattimura No. 20',

            'city' => 'Kota Jambi',

            'province' => 'Jambi',

            'postal_code' => '36122',

            'phone' => '081234567891',

            'email' => 'dinaspendidikan@gmail.com',

            'npwp' => '02.111.222.3-444.000',

            'contract_start' => now(),

            'contract_end' => now()->addMonths(12),

            'contract_status' => 'aktif',

            'status' => 'aktif',

            'approval_status' => 'disetujui',

            'approved_by' => $pimpinan->id,

            'approved_at' => now(),

            'description' => 'Audit kepatuhan instansi pemerintahan.',
        ]);

        // Tim audit
        if ($auditors->count() >= 3) {

            $client2->auditors()->attach(
                $auditors[1]->id,
                ['team_role' => 'ketua']
            );

            $client2->auditors()->attach(
                $auditors[2]->id,
                ['team_role' => 'anggota']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CV Teknologi Nusantara
        |--------------------------------------------------------------------------
        */
        $client3 = Client::create([

            'created_by' => $admin->id,

            'institution_type' => 'Swasta',

            'audit_type' => 'keuangan',

            'institution_name' => 'CV Teknologi Nusantara',

            'leader_name' => 'Siti Aminah',

            'address' => 'Jl. Anggrek No. 5',

            'city' => 'Bandung',

            'province' => 'Jawa Barat',

            'postal_code' => '40111',

            'phone' => '081234567892',

            'email' => 'teknologi@gmail.com',

            'npwp' => '03.555.666.7-888.000',

            'contract_start' => now(),

            'contract_end' => now()->addMonths(3),

            'contract_status' => 'aktif',

            'status' => 'aktif',

            'approval_status' => 'pending',

            'description' => 'Klien baru menunggu approval.',
        ]);

        // Tim audit
        if ($auditors->count() >= 3) {

            $client3->auditors()->attach(
                $auditors[2]->id,
                ['team_role' => 'ketua']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PT Sejahtera Abadi
        |--------------------------------------------------------------------------
        */
        $client4 = Client::create([

            'created_by' => $admin->id,

            'institution_type' => 'Swasta',

            'audit_type' => 'keuangan',

            'institution_name' => 'PT Sejahtera Abadi',

            'leader_name' => 'Rina Wijaya',

            'address' => 'Jl. Melati No. 15',

            'city' => 'Surabaya',

            'province' => 'Jawa Timur',

            'postal_code' => '60111',

            'phone' => '081234567893',

            'email' => 'sejahtera@gmail.com',

            'npwp' => '04.777.888.9-999.000',

            'contract_start' => now(),

            'contract_end' => now()->addMonths(9),

            'contract_status' => 'aktif',

            'status' => 'aktif',

            'approval_status' => 'pending',

            'description' => 'Klien audit baru.',
        ]);

        // Tim audit
        if ($auditors->count() >= 2) {

            $client4->auditors()->attach(
                $auditors[1]->id,
                ['team_role' => 'ketua']
            );

            $client4->auditors()->attach(
                $auditors[0]->id,
                ['team_role' => 'anggota']
            );
        }
    }
}
