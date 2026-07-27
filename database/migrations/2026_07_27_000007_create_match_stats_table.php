<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->integer('possession')->default(0);
            $table->integer('shots_total')->default(0);
            $table->integer('shots_on_target')->default(0);
            $table->integer('shots_off_target')->default(0);
            $table->integer('corners')->default(0);
            $table->integer('fouls')->default(0);
            $table->integer('offsides')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);
            $table->integer('passes_total')->default(0);
            $table->integer('passes_accurate')->default(0);
            $table->integer('tackles')->default(0);
            $table->integer('saves')->default(0);
            $table->integer('hit_woodwork')->default(0);
            $table->integer('blocked_shots')->default(0);
            $table->timestamps();
            $table->unique(['match_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_stats');
    }
};
