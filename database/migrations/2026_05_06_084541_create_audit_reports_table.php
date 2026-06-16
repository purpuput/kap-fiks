<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations
     */
    public function up(): void
    {
        Schema::create('audit_reports', function (Blueprint $table) {

            $table->id();

            // Client terkait
            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            // Auditor pembuat laporan
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // Pimpinan reviewer
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Nomor laporan
            $table->string('report_number')->unique();

            // Judul audit
            $table->string('title');

            // Jenis audit
            $table->enum('audit_type', [
                'keuangan',
                'kepatuhan'
            ]);

            // Tanggal audit
            $table->date('audit_date');

            // Temuan audit
            $table->longText('findings');

            // Rekomendasi
            $table->longText('recommendations')
                ->nullable();

            // Status workflow
            $table->enum('status', [

                'review',
                'revisi',
                'disetujui'

            ])->default('review');

            // Catatan revisi pimpinan
            $table->text('revision_note')
                ->nullable();

            // Tanggal review
            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_reports');
    }
};
