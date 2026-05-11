<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_report_files', function (Blueprint $table) {

            $table->id();

            $table->foreignId('audit_report_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('document_name');

            $table->enum('document_type', [
                'spk',
                'surat_tugas',
                'invoice',
                'laporan_keuangan',
                'kertas_kerja',
                'berita_acara_pemeriksaan',
                'data_klien',
                'surat_pernyataan_klien',
                'management_letter',
                'laporan_audit',
            ]);

            $table->string('file');

            $table->integer('version')->default(1);

            $table->boolean('is_active')->default(true);

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_report_files');
    }
};
