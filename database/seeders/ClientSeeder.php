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

        Client::create([
            'user_id' => $admin->id,

            'institution_type' => 'Swasta',

            'institution_name' => 'PT Maju Jaya',

            'leader_name' => 'Budi Santoso',

            'address' => 'Jl. Sudirman No. 10',

            'city' => 'Jambi',

            'province' => 'Jambi',

            'postal_code' => '36111',

            'phone' => '081234567890',

            'email' => 'majujaya@gmail.com',

            'npwp' => '01.234.567.8-999.000',

            'status' => 'aktif',

            'approval_status' => 'disetujui',

            'approved_by' => $admin->id,

            'approved_at' => now(),

            'description' => 'Klien audit keuangan'
        ]);

        Client::create([
            'user_id' => $admin->id,

            'institution_type' => 'Pemerintahan',

            'institution_name' => 'Dinas Pendidikan Kota Jambi',

            'leader_name' => 'Ahmad Rizki',

            'address' => 'Jl. Pattimura No. 20',

            'city' => 'Jambi',

            'province' => 'Jambi',

            'postal_code' => '36122',

            'phone' => '081234567891',

            'email' => 'dinaspendidikan@gmail.com',

            'npwp' => '02.111.222.3-444.000',

            'status' => 'aktif',

            'approval_status' => 'disetujui',

            'approved_by' => $admin->id,

            'approved_at' => now(),

            'description' => 'Audit kepatuhan'
        ]);

        Client::create([
            'user_id' => $admin->id,

            'institution_type' => 'Swasta',

            'institution_name' => 'CV Teknologi Nusantara',

            'leader_name' => 'Siti Aminah',

            'address' => 'Jl. Anggrek No. 5',

            'city' => 'Bandung',

            'province' => 'Jawa Barat',

            'postal_code' => '40111',

            'phone' => '081234567892',

            'email' => 'teknologi@gmail.com',

            'npwp' => '03.555.666.7-888.000',

            'status' => 'aktif',

            'approval_status' => 'pending',

            'description' => 'Klien baru'
        ]);

        Client::create([
            'user_id' => $admin->id,

            'institution_type' => 'Swasta',

            'institution_name' => 'PT Sejahtera Abadi',

            'leader_name' => 'Rina Wijaya',

            'address' => 'Jl. Melati No. 15',

            'city' => 'Surabaya',

            'province' => 'Jawa Timur',

            'postal_code' => '60111',

            'phone' => '081234567893',

            'email' => 'sejahtera@gmail.com',

            'npwp' => '04.777.888.9-999.000',

            'status' => 'aktif',

            'approval_status' => 'pending',

            'description' => 'Klien baru'
        ]);
    }
}
