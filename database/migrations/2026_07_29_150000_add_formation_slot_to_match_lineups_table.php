<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_lineups', function (Blueprint $table) {
            $table->integer('formation_slot')->nullable()->after('jersey_number');
        });
    }

    public function down(): void
    {
        Schema::table('match_lineups', function (Blueprint $table) {
            $table->dropColumn('formation_slot');
        });
    }
};
