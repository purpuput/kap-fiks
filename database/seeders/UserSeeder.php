<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'pimpinan',
            'email' => 'pimpinan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
        ]);

        User::create([
            'name' => 'mput',
            'email' => 'auditor1@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);

        User::create([
            'name' => 'puput',
            'email' => 'auditor2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);

        User::create([
            'name' => 'auditor',
            'email' => 'auditor3@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);
    }
}
