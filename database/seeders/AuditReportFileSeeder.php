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
     * Tipe dokumen audit
     */
    protected array $documentTypes = [

        'spk'                      => 'SPK',

        'surat_tugas'              => 'Surat Tugas',

        'invoice'                  => 'Invoice',

        'laporan_keuangan'         => 'Laporan Keuangan',

        'kertas_kerja'             => 'Kertas Kerja',

        'berita_acara_pemeriksaan' => 'Berita Acara Pemeriksaan',

        'data_klien'               => 'Data Klien',

        'surat_pernyataan_klien'   => 'Surat Pernyataan Klien',

        'management_letter'        => 'Management Letter',

        'laporan_audit'            => 'Laporan Audit',
    ];

    public function run(): void
    {
        // User auditor
        $auditor = User::where('role', 'auditor')->first();

        // User pimpinan
        $pimpinan = User::where('role', 'pimpinan')->first();

        if (!$auditor || !$pimpinan) {

            $this->command->warn(
                'Seeder membutuhkan user auditor dan pimpinan.'
            );

            return;
        }

        // Semua laporan audit
        $reports = AuditReport::all();

        foreach ($reports as $report) {

            // Gunakan creator report sebagai auditor uploader
            $auditor = User::find($report->created_by);

            if (!$auditor) {
                // Fallback ke auditor pertama client
                $auditor = $report->client->auditors->first() ?? User::where('role', 'auditor')->first();
            }

            $this->seedFilesForReport(
                $report,
                $auditor,
                $pimpinan
            );
        }
    }

    /**
     * Seeder file laporan
     */
    protected function seedFilesForReport(
        AuditReport $report,
        User $auditor,
        User $pimpinan
    ): void {

        $prefix = strtolower(
            str_replace(
                ['/', ' '],
                ['_', '_'],
                $report->report_number
            )
        );

        foreach ($this->documentTypes as $type => $label) {

            // Ekstensi file
            $ext = match ($type) {

                'data_klien' => 'zip',

                'laporan_keuangan',
                'kertas_kerja' => 'xlsx',

                default => 'pdf',
            };

            $fileName = "{$prefix}_{$type}.{$ext}";

            $fakePath = "audit_reports/{$report->id}/{$fileName}";

            // File audit
            $file = AuditReportFile::create([

                'audit_report_id' => $report->id,

                'document_name' => $fileName,

                'document_type' => $type,

                'file' => $fakePath,

                'version' => 1,

                'is_active' => true,

                'status' => $report->status === 'disetujui' ? 'lengkap' : 'pending',

                'uploaded_by' => $auditor->id,
            ]);

            // Arsip dokumen
            if ($report->status === 'disetujui') {

                DocumentManagement::create([

                    'audit_report_id' => $report->id,

                    'document_name' => $fileName,

                    'document_type' => $type,

                    'file' => $fakePath,

                    'archived_by' => $pimpinan->id,

                    'archived_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
