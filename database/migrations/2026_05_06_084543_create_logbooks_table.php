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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->string('activity');
            $table->text('description');
            $table->decimal('hours', 5, 2);
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
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
