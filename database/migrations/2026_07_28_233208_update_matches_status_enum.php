<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE matches MODIFY COLUMN status ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'postponed', 'abandoned', 'pending') NOT NULL DEFAULT 'scheduled'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE matches MODIFY COLUMN status ENUM('scheduled', 'in_progress', 'completed') NOT NULL DEFAULT 'scheduled'");
        }
    }
};
