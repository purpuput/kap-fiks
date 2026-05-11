<?php

namespace Database\Seeders;

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
        // Seeder user HARUS pertama
        $this->call(UserSeeder::class);

        // Seeder lain setelah user tersedia
        $this->call(AttendanceSeeder::class);

        $this->call(LogbookSeeder::class);

        $this->call(ClientSeeder::class);

        $this->call(AuditReportSeeder::class);

        // Seeder file laporan (10 dokumen per laporan) + document managements
        $this->call(AuditReportFileSeeder::class);
    }
}
