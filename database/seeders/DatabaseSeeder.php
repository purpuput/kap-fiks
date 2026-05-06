<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Data dummy user untuk setiap role
        // Password: password

        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory()->auditor()->create([
            'name' => 'Auditor User',
            'email' => 'auditor@example.com',
        ]);

        User::factory()->pimpinan()->create([
            'name' => 'Pimpinan User',
            'email' => 'pimpinan@example.com',
        ]);
    }
}
