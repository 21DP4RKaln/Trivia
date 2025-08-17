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
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->string('guest_identifier')->nullable()->after('user_id');
            $table->string('session_token', 64)->nullable()->unique()->after('guest_identifier');
            $table->json('game_state')->nullable()->after('session_token');
            $table->index(['session_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropIndex(['session_token']);
            $table->dropColumn(['guest_identifier', 'session_token', 'game_state']);
        });
    }
};
