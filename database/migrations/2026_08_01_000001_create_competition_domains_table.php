<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_domains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('name_fr')->nullable();
            $table->string('name_es')->nullable();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->string('evaluation_basis', 30)->default('match');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('competition_domains')->insert([
            [
                'name' => 'الرياضات',
                'name_en' => 'Sports',
                'name_fr' => 'Sports',
                'name_es' => 'Deportes',
                'slug' => 'sports',
                'icon' => 'bi-trophy',
                'description' => 'Football, futsal and other athletic competitions.',
                'evaluation_basis' => 'match',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الرياضات الإلكترونية',
                'name_en' => 'Esports',
                'name_fr' => 'Esports',
                'name_es' => 'Esports',
                'slug' => 'esports',
                'icon' => 'bi-controller',
                'description' => 'Competitive video gaming competitions and tournaments.',
                'evaluation_basis' => 'match',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'أكاديمي ومسابقات علمية',
                'name_en' => 'Academic & Quiz',
                'name_fr' => 'Académique & Quiz',
                'name_es' => 'Académico y Quiz',
                'slug' => 'academic',
                'icon' => 'bi-mortarboard',
                'description' => 'Academic olympiads, quiz bowls and knowledge competitions.',
                'evaluation_basis' => 'submission',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'هاكاثون',
                'name_en' => 'Hackathons',
                'name_fr' => 'Hackathons',
                'name_es' => 'Hackatones',
                'slug' => 'hackathon',
                'icon' => 'bi-code-slash',
                'description' => 'Coding marathons and innovation challenges.',
                'evaluation_basis' => 'submission',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'فنون إبداعية',
                'name_en' => 'Creative Arts',
                'name_fr' => 'Arts créatifs',
                'name_es' => 'Artes creativas',
                'slug' => 'creative',
                'icon' => 'bi-palette',
                'description' => 'Photography, writing, design and artistic competitions.',
                'evaluation_basis' => 'submission',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_domains');
    }
};
