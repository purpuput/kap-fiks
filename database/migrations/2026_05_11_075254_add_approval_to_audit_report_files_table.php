<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_report_files', function (Blueprint $table) {

            $table->enum('status', [
                'pending',
                'approve',
                'revisi'
            ])->default('pending');

            $table->text('revision_note')
                ->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('audit_report_files', function (Blueprint $table) {

            $table->dropColumn([
                'status',
                'revision_note',
                'approved_at'
            ]);

            $table->dropConstrainedForeignId('approved_by');
        });
    }
};
