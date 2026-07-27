<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_tactics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('pressing_style', ['high', 'medium', 'low', 'mixed'])->default('medium');
            $table->enum('build_up_style', ['from_back', 'quick_counter', 'long_ball', 'mixed'])->default('mixed');
            $table->enum('defense_style', ['zone', 'man_to_man', 'mixed'])->default('zone');
            $table->enum('attack_style', ['wing_play', 'central', 'balanced', 'counter_attack'])->default('balanced');
            $table->enum('formation_used', [
                '4-4-2', '4-3-3', '3-5-2', '4-2-3-1', '5-3-2', '4-1-4-1', '3-4-3',
                '4-1-2-3', '2-3-5', '4-4-1-1', '4-3-2-1', '3-4-2-1', '4-2-2-2',
                '4-0', '3-1', '2-2', '1-2-1', '2-1-1', '1-1-2',
            ])->nullable();
            $table->foreignId('match_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_tactics');
    }
};
