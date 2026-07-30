<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add 'captain' to team_staff.staff_role enum (MySQL only; SQLite stores as text)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE team_staff MODIFY COLUMN staff_role ENUM('head_coach','assistant_coach','goalkeeping_coach','fitness_coach','doctor','physiotherapist','admin','manager','nutritionist','analyst','captain') NOT NULL");
        }

        // 2. Add sport_id to players table
        Schema::table('players', function (Blueprint $table) {
            $table->foreignId('sport_id')->nullable()->constrained('sports')->nullOnDelete()->after('team_id');
        });

        // 3. Add sport_id to formations table
        Schema::table('formations', function (Blueprint $table) {
            $table->foreignId('sport_id')->nullable()->constrained('sports')->nullOnDelete()->after('team_id');
        });

        // Backfill: set sport_id from the team's sport_id (if available)
        $footballId = DB::table('sports')->where('slug', 'football')->value('id');
        if ($footballId) {
            DB::table('players')->whereNull('sport_id')->update(['sport_id' => $footballId]);
            DB::table('formations')->whereNull('sport_id')->update(['sport_id' => $footballId]);
        }
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });

        // Reset team_staff.staff_role enum to original values (without 'captain')
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE team_staff MODIFY COLUMN staff_role ENUM('head_coach','assistant_coach','goalkeeping_coach','fitness_coach','doctor','physiotherapist','admin','manager','nutritionist','analyst') NOT NULL");
        }
    }
};
