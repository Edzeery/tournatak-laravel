<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->json('position_categories')->nullable()->after('icon');
            $table->json('match_event_types')->nullable()->after('position_categories');
        });

        // ── Football defaults ──
        DB::table('sports')
            ->where('slug', 'football')
            ->update([
                'position_categories' => json_encode(['goalkeeper', 'defender', 'midfielder', 'forward']),
                'match_event_types' => json_encode(['goal', 'own_goal', 'penalty_scored', 'assist', 'yellow_card', 'second_yellow', 'red_card', 'substitution_in', 'substitution_out']),
            ]);

        // ── Futsal defaults ──
        DB::table('sports')
            ->where('slug', 'futsal')
            ->update([
                'position_categories' => json_encode(['goalkeeper', 'defender', 'forward']),
                'match_event_types' => json_encode(['goal', 'own_goal', 'penalty_scored', 'assist', 'yellow_card', 'second_yellow', 'red_card', 'substitution_in', 'substitution_out']),
            ]);
    }

    public function down(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->dropColumn(['position_categories', 'match_event_types']);
        });
    }
};
