<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judge_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('judge_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2)->unsigned()->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['submission_id', 'judge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('judge_scores');
    }
};
