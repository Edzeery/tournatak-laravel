<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_season_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->integer('season_year');
            $table->integer('matches_played')->default(0);
            $table->integer('matches_started')->default(0);
            $table->integer('minutes_played')->default(0);
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);
            $table->integer('saves')->default(0);
            $table->integer('clean_sheets')->default(0);
            $table->integer('tackles')->default(0);
            $table->integer('interceptions')->default(0);
            $table->integer('key_passes')->default(0);
            $table->integer('dribbles')->default(0);
            $table->timestamps();
            $table->unique(['player_id', 'competition_id', 'season_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_season_stats');
    }
};
