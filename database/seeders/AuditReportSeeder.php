<?php

namespace Database\Seeders;

use App\Models\AuditReport;
use App\Models\Client;
use Illuminate\Database\Seeder;

class AuditReportSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::with('auditors')->get();

        // Pastikan 2 laporan pertama disetujui
        $statuses = ['disetujui', 'disetujui', 'review', 'revisi'];

        foreach ($clients as $index => $client) {
            
            // Pastikan klien memiliki setidaknya satu auditor
            if ($client->auditors->count() > 0) {
                
                // Ambil auditor ketua tim, atau auditor pertama jika tidak ada
                $auditor = $client->auditors->where('pivot.team_role', 'ketua')->first() ?? $client->auditors->first();

                // Pilih status secara bergilir
                $status = $statuses[$index % count($statuses)];

                // Siapkan data dasar
                $reportData = [
                    'client_id' => $client->id,
                    'report_number' => 'AR/2026/' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'title' => 'Laporan Audit - ' . $client->institution_name,
                    'audit_type' => $client->audit_type,
                    'audit_date' => now(),
                    'findings' => 'Lihat pada dokumen lampiran.',
                    'recommendations' => 'Lihat pada dokumen lampiran.',
                    'status' => $status,
                    'created_by' => $auditor->id,
                ];

                // Jika statusnya disetujui, tambahkan reviewer
                if ($status === 'disetujui') {
                    $reportData['reviewed_by'] = 2; // Asumsi ID 2 adalah pimpinan
                    $reportData['reviewed_at'] = now();
                }

                // Jika statusnya revisi, tambahkan catatan
                if ($status === 'revisi') {
                    $reportData['revision_note'] = 'Mohon lengkapi bagian lampiran sesuai dengan standar yang berlaku.';
                }

                AuditReport::create($reportData);
            }
        }
    }
}
