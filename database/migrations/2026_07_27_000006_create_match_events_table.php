<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->enum('event_type', [
                'goal', 'own_goal', 'penalty_scored', 'penalty_missed',
                'yellow_card', 'second_yellow', 'red_card',
                'substitution_in', 'substitution_out',
                'injury', 'save', 'assist',
            ]);
            $table->integer('minute');
            $table->integer('added_time')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('related_player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_events');
    }
};
