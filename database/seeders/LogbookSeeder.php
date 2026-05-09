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

        /**
         * Aktivitas contoh
         */
        $activities = [
            'Melakukan pemeriksaan laporan kas dan bank perusahaan.',
            'Audit transaksi pembelian dan penjualan perusahaan.',
            'Pemeriksaan dokumen bukti transaksi klien.',
            'Penyusunan working paper audit tahunan.',
            'Melakukan stock opname persediaan barang.',
            'Pemeriksaan laporan pajak perusahaan.',
            'Audit laporan keuangan akhir tahun.',
            'Pemeriksaan piutang dan hutang perusahaan.',
            'Melakukan wawancara dengan bagian keuangan klien.',
            'Pemeriksaan rekonsiliasi bank perusahaan.',
        ];

        /**
         * Catatan revisi contoh
         */
        $revisionNotes = [
            'Tambahkan detail hasil pemeriksaan.',
            'Dokumen pendukung audit belum lengkap.',
            'Mohon revisi bagian aktivitas audit.',
            'Tambahkan bukti pemeriksaan transaksi.',
            'Perjelas hasil audit pada aktivitas.',
        ];

        /**
         * Generate data untuk setiap auditor
         */
        foreach ($auditors as $auditor) {

            /**
             * Generate 15 logbook
             */
            for ($i = 1; $i <= 15; $i++) {

                /**
                 * Random status
                 */
                $statuses = ['pending', 'approved', 'revision'];

                $status = $statuses[array_rand($statuses)];

                Logbook::create([

                    'user_id' => $auditor->id,

                    'date' => Carbon::now()
                        ->subDays(rand(1, 30)),

                    'activity' => $activities[array_rand($activities)],

                    'status' => $status,

                    'note' => $status === 'revision'
                        ? $revisionNotes[array_rand($revisionNotes)]
                        : null,

                    'created_at' => now(),
                    'updated_at' => now(),

                ]);
            }
        }
    }
}
