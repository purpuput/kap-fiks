<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {

            $table->enum('approval_status', [
                'pending',
                'disetujui',
                'revisi'
            ])->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->text('revision_note')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {

            $table->dropForeign(['approved_by']);

            $table->dropColumn([
                'approval_status',
                'approved_by',
                'approved_at',
                'revision_note'
            ]);
        });
    }
};
