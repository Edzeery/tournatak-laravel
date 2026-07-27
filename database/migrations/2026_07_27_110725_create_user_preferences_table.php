<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('locale', 5)->default('ar');
            $table->enum('theme', ['light', 'dark', 'system'])->default('system');
            $table->string('timezone')->default('Africa/Algiers');
            $table->string('date_format')->default('d/m/Y');
            $table->boolean('notify_email')->default(true);
            $table->boolean('notify_push')->default(false);
            $table->boolean('sidebar_collapsed')->default(false);
            $table->enum('density', ['comfortable', 'compact'])->default('comfortable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
