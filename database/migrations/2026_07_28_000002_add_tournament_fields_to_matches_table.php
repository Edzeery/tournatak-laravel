<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->integer('round')->nullable()->after('competition_id');
            $table->string('group_name', 10)->nullable()->after('round');
            $table->string('stage', 50)->nullable()->after('group_name');
            $table->tinyInteger('leg')->nullable()->after('stage');
            $table->string('bracket', 50)->nullable()->after('leg');
            $table->boolean('is_bye')->default(false)->after('bracket');
            $table->boolean('is_third_place')->default(false)->after('is_bye');
            $table->json('extra_data')->nullable()->after('is_third_place');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'round', 'group_name', 'stage', 'leg', 'bracket',
                'is_bye', 'is_third_place', 'extra_data',
            ]);
        });
    }
};
