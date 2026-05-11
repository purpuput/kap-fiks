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
        Schema::table('chat_messages', function (Blueprint $table) {
            // Delete old data if any because of relation
            \Illuminate\Support\Facades\DB::table('chat_messages')->truncate();
            
            $table->dropColumn('user_id');
            $table->foreignId('chat_session_id')->after('id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['chat_session_id']);
            $table->dropColumn('chat_session_id');
            $table->foreignId('user_id')->nullable();
        });
    }
};
