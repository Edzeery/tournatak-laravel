<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('staff_role', [
                'head_coach',
                'assistant_coach',
                'goalkeeping_coach',
                'fitness_coach',
                'doctor',
                'physiotherapist',
                'admin',
                'manager',
                'nutritionist',
                'analyst',
            ]);
            $table->string('specialization')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['team_id', 'user_id', 'staff_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_staff');
    }
};
