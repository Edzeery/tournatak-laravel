<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->after('team_id')->constrained()->nullOnDelete();
            $table->date('date_of_birth')->nullable()->after('position_id');
            $table->string('nationality')->nullable()->after('date_of_birth');
            $table->integer('height')->nullable()->comment('cm')->after('nationality');
            $table->integer('weight')->nullable()->comment('kg')->after('height');
            $table->string('foot')->nullable()->comment('left/right/both')->after('weight');
            $table->enum('sport_type', ['football', 'futsal'])->default('football')->after('foot');
            $table->text('bio')->nullable()->after('sport_type');
            $table->boolean('is_captain')->default(false)->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'position_id', 'date_of_birth', 'nationality', 'height',
                'weight', 'foot', 'sport_type', 'bio', 'is_captain',
            ]);
        });
    }
};
