<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('name_fr')->nullable();
            $table->string('name_es')->nullable();
            $table->string('slug')->unique();
            $table->string('category', 30)->default('team');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('sports')->insert([
            [
                'name' => 'كرة القدم',
                'name_en' => 'Football',
                'name_fr' => 'Football',
                'name_es' => 'Fútbol',
                'slug' => 'football',
                'category' => 'team',
                'icon' => 'bi-soccer-ball',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'كرة الصالات',
                'name_en' => 'Futsal',
                'name_fr' => 'Futsal',
                'name_es' => 'Fútbol Sala',
                'slug' => 'futsal',
                'category' => 'team',
                'icon' => 'bi-soccer-ball',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sports');
    }
};
