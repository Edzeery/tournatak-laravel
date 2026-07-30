<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert ENUM columns to VARCHAR for cross-DB compatibility (SQLite does not support ENUM)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE matches MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'scheduled'");
            DB::statement("ALTER TABLE team_staff MODIFY COLUMN staff_role VARCHAR(50) NOT NULL DEFAULT 'head_coach'");
        } else {
            Schema::table('matches', function (Blueprint $table) {
                $table->string('status', 50)->default('scheduled')->change();
            });
            Schema::table('team_staff', function (Blueprint $table) {
                $table->string('staff_role', 50)->default('head_coach')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE matches MODIFY COLUMN status ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'postponed', 'abandoned', 'pending') NOT NULL DEFAULT 'scheduled'");
            DB::statement("ALTER TABLE team_staff MODIFY COLUMN staff_role ENUM('head_coach','assistant_coach','goalkeeping_coach','fitness_coach','doctor','physiotherapist','admin','manager','nutritionist','analyst','captain') NOT NULL DEFAULT 'head_coach'");
        }
    }
};
