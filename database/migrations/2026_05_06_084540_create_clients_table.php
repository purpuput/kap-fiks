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

            // Admin pembuat data
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // Jenis instansi
            $table->enum('institution_type', [

                'Pemerintahan',

                'Swasta'
            ]);

            // Jenis audit
            $table->enum('audit_type', [

                'keuangan',

                'kepatuhan'
            ]);

            // Nama instansi
            $table->string('institution_name');

            // Nama pimpinan
            $table->string('leader_name');

            // Alamat
            $table->text('address');

            // Kota
            $table->string('city');

            // Provinsi
            $table->string('province');

            // Kode pos
            $table->string('postal_code');

            // Telepon
            $table->string('phone');

            // Email
            $table->string('email')
                ->nullable();

            // NPWP
            $table->string('npwp')
                ->nullable();

            // Kontrak audit mulai
            $table->date('contract_start')
                ->nullable();

            // Kontrak audit selesai
            $table->date('contract_end')
                ->nullable();

            // Status kontrak
            $table->enum('contract_status', [

                'aktif',

                'hampir_habis',

                'selesai'

            ])->default('aktif');

            // Status client
            $table->enum('status', [

                'aktif',

                'nonaktif'

            ])->default('aktif');

            // Approval pimpinan
            $table->enum('approval_status', [

                'pending',

                'disetujui',

                'revisi'

            ])->default('pending');

            // Pimpinan approval
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Tanggal approval
            $table->timestamp('approved_at')
                ->nullable();

            // Catatan revisi
            $table->text('revision_note')
                ->nullable();

            // Deskripsi
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
