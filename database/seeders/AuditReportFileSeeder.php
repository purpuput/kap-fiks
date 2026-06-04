<?php

namespace Database\Seeders;

use App\Models\AuditReport;
use App\Models\AuditReportFile;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

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
        // User admin
        $admin = User::where('role', 'admin')->first();

        // User auditor
        $auditor = User::where('role', 'auditor')->first();

        // User pimpinan
        $pimpinan = User::where('role', 'pimpinan')->first();

        if (!$admin || !$auditor || !$pimpinan) {

            $this->command->warn(
                'Seeder membutuhkan user admin, auditor, dan pimpinan.'
            );

            return;
        }

        // Semua laporan audit
        $reports = AuditReport::all();

        foreach ($reports as $report) {

            // Ambil auditor pertama dari klien laporan tersebut, atau fallback ke auditor umum
            $clientAuditor = $report->client->auditors->first() ?? $auditor;

            $this->seedFilesForReport(
                $report,
                $clientAuditor,
                $pimpinan,
                $admin
            );
        }
    }

    /**
     * Seeder file laporan
     */
    protected function seedFilesForReport(
        AuditReport $report,
        User $auditor,
        User $pimpinan,
        User $admin
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

                default => 'pdf',
            };

            $fileName = "{$prefix}_{$type}.{$ext}";

            $fakePath = "audit_reports/{$report->id}/{$fileName}";

            // SPK, Surat Tugas, dan Invoice diupload oleh admin
            $uploadedBy = match ($type) {
                'spk', 'surat_tugas', 'invoice' => $admin->id,
                default => $auditor->id,
            };

            // Tentukan status review dokumen seeder
            $reviewStatus = 'pending_review';
            $reviewNote = null;
            $reviewerId = null;
            $reviewedAt = null;

            if ($report->status === 'disetujui') {
                $reviewStatus = 'approved';
                $reviewerId = $pimpinan->id;
                $reviewedAt = now();
            } elseif ($report->status === 'revisi') {
                // Buat salah satu tipe dokumen menjadi revision, yang lain approved
                if ($type === 'laporan_audit') {
                    $reviewStatus = 'revision';
                    $reviewNote = 'Mohon lengkapi bagian lampiran sesuai dengan standar yang berlaku.';
                    $reviewerId = $pimpinan->id;
                    $reviewedAt = now();
                } else {
                    $reviewStatus = 'approved';
                    $reviewerId = $pimpinan->id;
                    $reviewedAt = now();
                }
            }

            // File audit
            $file = AuditReportFile::create([
                'audit_report_id' => $report->id,
                'document_name' => $fileName,
                'document_type' => $type,
                'file' => $fakePath,
                'version' => 1,
                'is_active' => true,
                'status' => $reviewStatus === 'approved' ? 'lengkap' : ($reviewStatus === 'revision' ? 'belum lengkap' : 'pending'),
                'uploaded_by' => $uploadedBy,
                'approved_by' => $reviewStatus === 'approved' ? $pimpinan->id : null,
                'approved_at' => $reviewStatus === 'approved' ? now() : null,
            ]);

            // Buat record review dokumen
            \App\Models\DocumentReview::create([
                'audit_report_id' => $report->id,
                'audit_file_id' => $file->id,
                'reviewer_id' => $reviewerId,
                'status' => $reviewStatus,
                'note' => $reviewNote,
                'reviewed_at' => $reviewedAt,
            ]);

            // Buat file fisik dummy agar fungsi download & preview berfungsi
            Storage::disk('local')->put($fakePath, "Ini adalah file dummy untuk dokumen: {$fileName}.\nTipe dokumen: {$type}.\nStatus: " . ($report->status === 'disetujui' ? 'Lengkap' : 'Pending'));


        }
    }
}
