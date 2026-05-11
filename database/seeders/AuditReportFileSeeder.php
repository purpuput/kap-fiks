<?php

namespace Database\Seeders;

use App\Models\AuditReport;
use App\Models\AuditReportFile;
use App\Models\DocumentManagement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditReportFileSeeder extends Seeder
{
    /**
     * 10 tipe dokumen laporan audit
     */
    protected array $documentTypes = [
        'spk'                      => 'SPK (Surat Perintah Kerja)',
        'surat_tugas'              => 'Surat Tugas',
        'invoice'                  => 'Invoice',
        'laporan_keuangan'         => 'Laporan Keuangan',
        'kertas_kerja'             => 'Kertas Kerja',
        'berita_acara_pemeriksaan' => 'Berita Acara Pemeriksaan',
        'data_klien'               => 'Data yang Diterima dari Klien',
        'surat_pernyataan_klien'   => 'Surat Pernyataan Klien',
        'management_letter'        => 'Management Letter Temuan',
        'laporan_audit'            => 'Laporan Audit',
    ];

    public function run(): void
    {
        // Ambil user auditor dan pimpinan yang sudah ada
        $auditor  = User::where('role', 'auditor')->first();
        $pimpinan = User::where('role', 'pimpinan')->first();

        if (!$auditor || !$pimpinan) {
            $this->command->warn('Seeder membutuhkan user auditor dan pimpinan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Seeder untuk semua laporan yang ada
        $reports = AuditReport::all();

        foreach ($reports as $report) {
            $this->seedFilesForReport($report, $auditor, $pimpinan);
        }
    }

    protected function seedFilesForReport(AuditReport $report, User $auditor, User $pimpinan): void
    {
        $prefix = strtolower(str_replace(['/', ' '], ['_', '_'], $report->report_number));

        foreach ($this->documentTypes as $type => $label) {
            // Tentukan ekstensi file berdasarkan tipe
            $ext = match($type) {
                'data_klien'     => 'zip',
                'laporan_keuangan',
                'kertas_kerja'   => 'xlsx',
                default          => 'pdf',
            };

            $fileName = "{$prefix}_{$type}.{$ext}";
            $fakePath = "audit_reports/{$report->id}/{$fileName}";

            $file = AuditReportFile::create([
                'audit_report_id' => $report->id,
                'document_name'   => $fileName,
                'document_type'   => $type,
                'file'            => $fakePath,
                'version'         => 1,
                'is_active'       => true,
                'uploaded_by'     => $auditor->id,
            ]);

            // Untuk laporan yang sudah approve, simpan juga ke document_managements
            if ($report->status === 'approve') {
                DocumentManagement::create([
                    'audit_report_id' => $report->id,
                    'document_name'   => $fileName,
                    'document_type'   => $type,
                    'file'            => $fakePath,
                    'archived_by'     => $pimpinan->id,
                    'archived_at'     => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
