<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TokenQuota;
use App\Models\User;

class TokenQuotaSeeder extends Seeder
{
    /**
     * Seed the application's token quotas for 20 users
     */
    public function run(): void
    {
        // ============================================================
        // 3 ADMIN - Token limit: 50,000 per hari / 500,000 per bulan
        // ============================================================

        // Admin 1: Super Admin
        TokenQuota::updateOrCreate(
            ['user_id' => 1],
            [
                'role' => 'admin',
                'daily_limit' => 50000,
                'monthly_limit' => 500000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // Admin 2: System Admin
        TokenQuota::updateOrCreate(
            ['user_id' => 2],
            [
                'role' => 'admin',
                'daily_limit' => 50000,
                'monthly_limit' => 500000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // Admin 3: Data Admin
        TokenQuota::updateOrCreate(
            ['user_id' => 3],
            [
                'role' => 'admin',
                'daily_limit' => 50000,
                'monthly_limit' => 500000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // ============================================================
        // 5 PIMPINAN - Token limit: 30,000 per hari / 300,000 per bulan
        // ============================================================

        // Pimpinan 1: Manajer Audit
        TokenQuota::updateOrCreate(
            ['user_id' => 4],
            [
                'role' => 'pimpinan',
                'daily_limit' => 30000,
                'monthly_limit' => 300000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // Pimpinan 2: Supervisor Audit
        TokenQuota::updateOrCreate(
            ['user_id' => 5],
            [
                'role' => 'pimpinan',
                'daily_limit' => 30000,
                'monthly_limit' => 300000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // Pimpinan 3: Kepala Divisi Audit
        TokenQuota::updateOrCreate(
            ['user_id' => 6],
            [
                'role' => 'pimpinan',
                'daily_limit' => 30000,
                'monthly_limit' => 300000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // Pimpinan 4: Quality Assurance Manager
        TokenQuota::updateOrCreate(
            ['user_id' => 7],
            [
                'role' => 'pimpinan',
                'daily_limit' => 30000,
                'monthly_limit' => 300000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // Pimpinan 5: Project Manager
        TokenQuota::updateOrCreate(
            ['user_id' => 8],
            [
                'role' => 'pimpinan',
                'daily_limit' => 30000,
                'monthly_limit' => 300000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // ============================================================
        // 12 AUDITOR - Token limit: 8,000-15,000 per hari / 80,000-150,000 per bulan
        // ============================================================

        // Auditor 1: Senior Auditor / Lead
        TokenQuota::updateOrCreate(
            ['user_id' => 9],
            [
                'role' => 'auditor',
                'daily_limit' => 15000,        // Lebih tinggi karena lead
                'monthly_limit' => 150000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // Auditor 2: Senior Auditor / Lead
        TokenQuota::updateOrCreate(
            ['user_id' => 10],
            [
                'role' => 'auditor',
                'daily_limit' => 15000,
                'monthly_limit' => 150000,
                'used_today' => 0,
                'used_this_month' => 0,
            ]
        );

        // Auditor 3-8: Mid-Level Auditor
        for ($i = 11; $i <= 16; $i++) {
            TokenQuota::updateOrCreate(
                ['user_id' => $i],
                [
                    'role' => 'auditor',
                    'daily_limit' => 10000,     // Standard
                    'monthly_limit' => 100000,
                    'used_today' => 0,
                    'used_this_month' => 0,
                ]
            );
        }

        // Auditor 9-12: Junior Auditor
        for ($i = 17; $i <= 20; $i++) {
            TokenQuota::updateOrCreate(
                ['user_id' => $i],
                [
                    'role' => 'auditor',
                    'daily_limit' => 8000,      // Lebih rendah
                    'monthly_limit' => 80000,
                    'used_today' => 0,
                    'used_this_month' => 0,
                ]
            );
        }

        $this->command->info('✅ Token quotas untuk 20 users telah disetup!');

        // Tampilkan summary
        $this->displaySummary();
    }

    /**
     * Tampilkan summary quota yang telah dibuat
     */
    private function displaySummary(): void
    {
        $quotas = TokenQuota::all();

        $this->command->line('');
        $this->command->line('═══════════════════════════════════════════════════════════════');
        $this->command->line('📊 TOKEN QUOTA SUMMARY');
        $this->command->line('═══════════════════════════════════════════════════════════════');

        $byRole = $quotas->groupBy('role');

        foreach ($byRole as $role => $items) {
            $totalDaily = $items->sum('daily_limit');
            $totalMonthly = $items->sum('monthly_limit');
            $count = $items->count();

            $this->command->line("");
            $this->command->line("👤 <info>$role</info> (x$count users)");
            $this->command->line("   Daily Total:   $totalDaily tokens");
            $this->command->line("   Monthly Total: $totalMonthly tokens");
        }

        $this->command->line("");
        $this->command->line('═══════════════════════════════════════════════════════════════');
        $totalDaily = $quotas->sum('daily_limit');
        $totalMonthly = $quotas->sum('monthly_limit');
        $this->command->line("📈 <comment>GRAND TOTAL</comment>");
        $this->command->line("   Daily:   $totalDaily tokens");
        $this->command->line("   Monthly: $totalMonthly tokens");
        $this->command->line('═══════════════════════════════════════════════════════════════');
        $this->command->line("");
    }
}
