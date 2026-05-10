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
        Schema::create('clients', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('institution_type', [
                'Pemerintahan',
                'Swasta'
            ]);

            $table->string('institution_name');

            $table->string('leader_name');

            $table->text('address');

            $table->string('city');

            $table->string('province');

            $table->string('postal_code');

            $table->string('phone');

            $table->string('email')->nullable();

            $table->string('npwp')->nullable();

            $table->enum('status', [
                'aktif',
                'nonaktif'
            ])->default('aktif');

            // approval
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

            $table->text('description')
                ->nullable();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
