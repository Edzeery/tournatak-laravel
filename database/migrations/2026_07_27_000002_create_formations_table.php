<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('sport_type', ['football', 'futsal']);
            $table->enum('formation_code', [
                '4-4-2', '4-3-3', '3-5-2', '4-2-3-1', '5-3-2', '4-1-4-1', '3-4-3',
                '4-1-2-3', '2-3-5', '4-4-1-1', '4-3-2-1', '3-4-2-1', '4-2-2-2',
                '4-0', '3-1', '2-2', '1-2-1', '2-1-1', '1-1-2',
            ]);
            $table->json('positions_data');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
