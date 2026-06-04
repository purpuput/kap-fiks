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
        Schema::create('document_reviews', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('audit_report_id')
                ->constrained('audit_reports')
                ->onDelete('cascade');
                
            $table->foreignId('audit_file_id')
                ->constrained('audit_report_files')
                ->onDelete('cascade');
                
            $table->foreignId('reviewer_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
                
            $table->enum('status', ['approved', 'revision', 'pending_review'])
                ->default('pending_review');
                
            $table->boolean('is_viewed')->default(false);
                
            $table->text('note')->nullable();
            
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_reviews');
    }
};
