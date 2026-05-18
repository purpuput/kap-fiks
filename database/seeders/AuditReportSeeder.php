<?php

namespace Database\Seeders;

use App\Models\AuditReport;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditReportSeeder extends Seeder
{
    public function run(): void
    {
        $report1 = AuditReport::create([
            'client_id' => 1,
            'report_number' => 'AR/2026/001',
            'title' => 'Audit Keuangan PT Maju Jaya',
            'audit_type' => 'keuangan',
            'audit_date' => now(),
            'findings' => 'Ditemukan ketidaksesuaian laporan kas.',
            'recommendations' => 'Perbaiki pencatatan kas harian.',
            'status' => 'pending',
            'created_by' => 1,
        ]);

        $report2 = AuditReport::create([
            'client_id' => 2,
            'report_number' => 'AR/2026/002',
            'title' => 'Audit Kepatuhan PT Sumber Rezeki',
            'audit_type' => 'kepatuhan',
            'audit_date' => now(),
            'findings' => 'Dokumen SOP belum lengkap.',
            'recommendations' => 'Lengkapi SOP operasional.',
            'status' => 'approve',
            'created_by' => 1,
        ]);

        $report3 = AuditReport::create([
            'client_id' => 3,
            'report_number' => 'AR/2026/003',
            'title' => 'Audit Internal CV Teknologi Nusantara',
            'audit_type' => 'keuangan',
            'audit_date' => now(),
            'findings' => 'Pengeluaran tidak memiliki bukti.',
            'recommendations' => 'Lampirkan bukti transaksi.',
            'status' => 'revisi',
            'revision_note' => 'Perbaiki detail transaksi.',
            'created_by' => 1,
        ]);

        $report4 = AuditReport::create([
            'client_id' => 4,
            'report_number' => 'AR/2026/004',
            'title' => 'Audit Keuangan PT Sejahtera Abadi',
            'audit_type' => 'keuangan',
            'audit_date' => now(),
            'findings' => 'Laporan keuangan sudah sesuai.',
            'recommendations' => 'Tidak ada rekomendasi.',
            'status' => 'approve',
            'created_by' => 1,
        ]);



        // Auditor
        $report1->auditors()->sync([3, 4]);

        $report2->auditors()->sync([4]);

        $report3->auditors()->sync([3, 5]);

        $report4->auditors()->sync([4]);
    }
}
