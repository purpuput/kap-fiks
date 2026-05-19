<?php

namespace Database\Seeders;

use App\Models\Logbook;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LogbookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Ambil semua auditor
         */
        $auditors = User::where('role', 'auditor')->get();

        /**
         * Jika auditor kosong
         */
        if ($auditors->isEmpty()) {
            return;
        }

        $auditStages = ['Perencanaan Audit', 'Pemahaman Bisnis Klien', 'Pengujian Pengendalian', 'Pengujian Substantif', 'Penyusunan KKA', 'Review Internal', 'Penyusunan Laporan'];
        $auditAreas = ['Kas & Bank', 'Piutang', 'Persediaan', 'Asset Tetap', 'Utang', 'Pendapatan', 'Beban', 'Lainnya'];
        
        $mainTasks = [
            'Melakukan pemeriksaan laporan kas dan bank perusahaan.',
            'Audit transaksi pembelian dan penjualan perusahaan.',
            'Pemeriksaan dokumen bukti transaksi klien.',
            'Penyusunan working paper audit tahunan.',
            'Melakukan stock opname persediaan barang.',
        ];

        $obstacleTypes = ['Data klien belum lengkap', 'Klien sulit dihubungi', 'Sistem klien bermasalah', 'Waktu terbatas', 'Masalah teknis audit', 'Lainnya'];
        $helpNeededOptions = ['Bantuan senior', 'Eskalasi ke manajer', 'Koordinasi dengan partner', 'Perpanjangan Waktu', 'Tidak perlu bantuan'];

        /**
         * Catatan revisi contoh
         */
        $revisionNotes = [
            'Tambahkan detail hasil pemeriksaan.',
            'Dokumen pendukung audit belum lengkap.',
            'Mohon revisi bagian aktivitas audit.',
        ];

        /**
         * Generate data untuk setiap auditor
         */
        foreach ($auditors as $auditor) {
            
            $clients = $auditor->clients()->where('approval_status', 'disetujui')->where('status', 'aktif')->get();
            if ($clients->isEmpty()) {
                continue;
            }

            /**
             * Generate 10 logbook per auditor
             */
            for ($i = 1; $i <= 10; $i++) {

                $client = $clients->random();
                $pivotRole = $client->pivot->team_role ?? 'anggota';
                $teamRole = $pivotRole === 'ketua' ? 'ketua tim' : 'anggota tim';
                
                $statuses = ['pending', 'approved', 'revision'];
                $status = $statuses[array_rand($statuses)];

                $hasObstacle = rand(0, 1) == 1;

                Logbook::create([
                    'user_id' => $auditor->id,
                    'client_id' => $client->id,
                    'team_role' => $teamRole,
                    'date' => Carbon::now()->subDays(rand(1, 30)),
                    'audit_stage' => $auditStages[array_rand($auditStages)],
                    'audit_area' => $auditAreas[array_rand($auditAreas)],
                    'cumulative_progress' => rand(10, 100),
                    'main_task' => $mainTasks[array_rand($mainTasks)],
                    
                    'has_obstacle' => $hasObstacle,
                    'obstacle_type' => $hasObstacle ? $obstacleTypes[array_rand($obstacleTypes)] : null,
                    'obstacle_description' => $hasObstacle ? 'Penjelasan singkat tentang kendala yang dialami.' : null,
                    'help_needed' => $hasObstacle ? $helpNeededOptions[array_rand($helpNeededOptions)] : null,
                    
                    'next_plan' => 'Melanjutkan pemeriksaan tahap selanjutnya.',
                    
                    'status' => $status,
                    'note' => $status === 'revision' ? $revisionNotes[array_rand($revisionNotes)] : null,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
