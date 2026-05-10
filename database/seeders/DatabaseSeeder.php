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
            'name' => 'admin',
            'email' => 'admin@example.com',
        ]);

        User::factory()->auditor()->create([
            'name' => 'auditor',
            'email' => 'auditor@example.com',
        ]);

        User::factory()->pimpinan()->create([
            'name' => 'pimpinan',
            'email' => 'pimpinan@example.com',
        ]);

        // Seeder attendance
        $this->call(AttendanceSeeder::class);

        // Seeder logbook
        $this->call(LogbookSeeder::class);

        // Seeder client
        $this->call(ClientSeeder::class);
    }
}
