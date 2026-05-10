<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $auditor = User::where('role', 'auditor')->first();

        Client::create([
            'user_id' => $admin->id,

            'institution_type' => 'Pemerintahan',

            'institution_name' => 'Dinas Pendidikan Kota Jambi',

            'leader_name' => 'Drs. Ahmad Fauzi',

            'address' => 'Jl. Pattimura No. 10 Kota Jambi',

            'city' => 'Jambi',

            'province' => 'Jambi',

            'postal_code' => '36122',

            'phone' => '0741123456',

            'email' => 'disdik@jambi.go.id',

            'npwp' => '01.234.567.8-123.000',

            'status' => 'aktif',

            'approval_status' => 'disetujui',

            'approved_by' => $admin->id,

            'approved_at' => now(),

            'description' => 'Audit laporan keuangan tahunan.',
        ]);

        Client::create([
            'user_id' => $auditor->id,

            'institution_type' => 'Swasta',

            'institution_name' => 'PT Maju Bersama',

            'leader_name' => 'Budi Santoso',

            'address' => 'Jl. Sudirman No. 20 Jakarta',

            'city' => 'Jakarta',

            'province' => 'DKI Jakarta',

            'postal_code' => '10220',

            'phone' => '02199887766',

            'email' => 'info@majubersama.com',

            'npwp' => '02.987.654.3-456.000',

            'status' => 'aktif',

            'approval_status' => 'pending',

            'description' => 'Audit operasional perusahaan.',
        ]);

        Client::create([
            'user_id' => $auditor->id,

            'institution_type' => 'Swasta',

            'institution_name' => 'CV Sinar Teknologi',

            'leader_name' => 'Rina Amelia',

            'address' => 'Jl. Gatot Subroto No. 88 Bandung',

            'city' => 'Bandung',

            'province' => 'Jawa Barat',

            'postal_code' => '40211',

            'phone' => '022667788',

            'email' => 'admin@sinartekno.id',

            'npwp' => '03.111.222.3-789.000',

            'status' => 'nonaktif',

            'approval_status' => 'revisi',

            'revision_note' => 'Mohon lengkapi dokumen NPWP perusahaan.',

            'description' => 'Audit internal divisi IT.',
        ]);
    }
}
