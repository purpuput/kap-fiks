<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 2 Admin
        User::create([
            'name' => 'Bambang Haryanto',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Siti Rahmawati',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 1 Pimpinan
        User::create([
            'name' => 'Eko Prasetyo',
            'email' => 'pimpinan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
        ]);

        // 6 Auditor
        User::create([
            'name' => 'Puput Lestari',
            'email' => 'auditor1@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);

        User::create([
            'name' => 'Muhammad Rizky',
            'email' => 'auditor2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);

        User::create([
            'name' => 'Anisa Fitriani',
            'email' => 'auditor3@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);

        User::create([
            'name' => 'Dewi Anggraini',
            'email' => 'auditor4@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);

        User::create([
            'name' => 'Fajar Pratama',
            'email' => 'auditor5@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);

        User::create([
            'name' => 'Bagas Saputra',
            'email' => 'auditor6@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);
    }
}
