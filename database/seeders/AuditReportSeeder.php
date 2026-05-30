<?php

namespace Database\Seeders;

use App\Models\AuditReport;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditReportSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::query()->with('auditors')->get();
        $admin = User::query()->where('role', 'admin')->first();

        foreach ($clients as $index => $client) {
            // Pastikan klien memiliki setidaknya satu auditor
            if ($client->auditors->count() > 0) {
                
                // Ambil auditor ketua tim, atau auditor pertama jika tidak ada
                $auditor = $client->auditors->where('pivot.team_role', 'ketua')->first() ?? $client->auditors->first();

                // Pastikan 2 laporan pertama disetujui, pilih status secara bergilir
                $statuses = ['disetujui', 'disetujui', 'review', 'revisi'];
                $status = $statuses[$index % count($statuses)];

                // Siapkan data dasar
                $formattedNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $auditType = $client->audit_type === 'keuangan' ? 'KEU' : 'KPT';
                $month = date('m');
                $year = date('Y');
                $reportNumber = "{$formattedNumber}/LA/{$auditType}/{$month}/{$year}";

                $reportData = [
                    'client_id' => $client->id,
                    'report_number' => $reportNumber,
                    'title' => 'Laporan Audit - ' . $client->institution_name,
                    'audit_type' => $client->audit_type,
                    'audit_date' => now(),
                    'findings' => 'Lihat pada dokumen lampiran.',
                    'recommendations' => 'Lihat pada dokumen lampiran.',
                    'status' => $status,
                    'created_by' => $admin ? $admin->id : $auditor->id,
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
