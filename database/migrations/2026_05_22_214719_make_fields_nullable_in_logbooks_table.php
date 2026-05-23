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
        Schema::table('logbooks', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->change();
            $table->string('team_role')->nullable()->change();
            $table->string('audit_stage')->nullable()->change();
            $table->string('audit_area')->nullable()->change();
            $table->integer('cumulative_progress')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            $table->string('team_role')->nullable(false)->change();
            $table->string('audit_stage')->nullable(false)->change();
            $table->string('audit_area')->nullable(false)->change();
            $table->integer('cumulative_progress')->nullable(false)->change();
        });
    }
};
