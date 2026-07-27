<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_lineups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_starter')->default(true);
            $table->integer('jersey_number')->nullable();
            $table->integer('minute_in')->nullable();
            $table->integer('minute_out')->nullable();
            $table->enum('sub_reason', ['tactical', 'injury', 'red_card', 'yellow_card', 'fatigue'])->nullable();
            $table->boolean('is_captain')->default(false);
            $table->text('performance_notes')->nullable();
            $table->timestamps();
            $table->unique(['match_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_lineups');
    }
};
