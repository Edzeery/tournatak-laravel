<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->foreignId('player_id')->nullable()->change();
            $table->foreign('player_id')->references('id')->on('players')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->foreignId('player_id')->nullable(false)->change();
            $table->foreign('player_id')->references('id')->on('players')->cascadeOnDelete();
        });
    }
};
