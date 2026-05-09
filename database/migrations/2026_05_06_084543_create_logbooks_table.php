<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {

            $table->id();

            /**
             * Relasi user auditor
             */
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            /**
             * Tanggal logbook
             */
            $table->date('date');

            /**
             * Aktivitas auditor
             */
            $table->text('activity');

            /**
             * Status approval
             */
            $table->enum('status', [
                'pending',
                'approved',
                'revision'
            ])->default('pending');

            /**
             * Catatan revisi pimpinan
             */
            $table->text('note')->nullable();

            $table->timestamps();

            /**
             * Index
             */
            $table->index('date');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
