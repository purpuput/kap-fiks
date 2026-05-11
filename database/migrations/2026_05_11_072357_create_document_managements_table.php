<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_managements', function (Blueprint $table) {

            $table->id();

            $table->foreignId('audit_report_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('document_name');

            $table->string('document_type');

            $table->string('file');

            $table->foreignId('archived_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamp('archived_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_managements');
    }
};
