<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->enum('record_type', ['injury', 'illness', 'checkup', 'surgery', 'rehabilitation']);
            $table->string('injury_name');
            $table->enum('severity', ['minor', 'moderate', 'severe', 'critical']);
            $table->enum('status', ['active', 'recovering', 'returned', 'long_term']);
            $table->date('injury_date');
            $table->date('expected_return')->nullable();
            $table->date('actual_return')->nullable();
            $table->text('treatment')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_medical_records');
    }
};
