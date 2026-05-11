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
        Schema::create('audit_reports', function (Blueprint $table) {

            $table->id();

            $table->foreignId('client_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('report_number')->unique();

            $table->string('title');

            $table->enum('audit_type', [
                'keuangan',
                'kepatuhan'
            ]);

            $table->date('audit_date');

            $table->text('findings');

            $table->text('recommendations')->nullable();

            $table->enum('status', [
                'pending',
                'approve',
                'revisi'
            ])->default('pending');

            $table->text('revision_note')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_reports');
    }
};
