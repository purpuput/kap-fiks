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

            // admin pembuat data
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

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

            // masa kontrak audit
            $table->date('contract_start')->nullable();

            $table->date('contract_end')->nullable();

            // status kontrak
            $table->enum('contract_status', [
                'aktif',
                'hampir_habis',
                'selesai'
            ])->default('aktif');

            $table->enum('status', [
                'aktif',
                'nonaktif'
            ])->default('aktif');

            $table->text('description')->nullable();

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
