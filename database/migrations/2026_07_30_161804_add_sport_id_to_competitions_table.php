<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->foreignId('sport_id')->nullable()->constrained('sports')->nullOnDelete()->after('organizer_id');
        });

        // Backfill: existing competitions reference football (slug = 'football')
        $footballId = DB::table('sports')->where('slug', 'football')->value('id');
        if ($footballId) {
            DB::table('competitions')->whereNull('sport_id')->update(['sport_id' => $footballId]);
        }
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });
    }
};
