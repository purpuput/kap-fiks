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
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('cascade');
            $table->enum('team_role', ['ketua tim', 'anggota tim']);
            $table->date('date');
            
            $table->string('audit_stage');
            $table->string('audit_area');
            $table->integer('cumulative_progress')->comment('Progress in percentage 1-100');
            $table->text('main_task');
            
            $table->boolean('has_obstacle')->default(false);
            $table->string('obstacle_type')->nullable();
            $table->text('obstacle_description')->nullable();
            $table->string('help_needed')->nullable();
            
            $table->text('next_plan');
            
            $table->enum('status', [
                'pending',
                'approved',
                'revision'
            ])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
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
