<?php

use App\Models\MatchEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('goals')) {
            return;
        }

        $goals = DB::table('goals')->get();

        foreach ($goals as $goal) {
            $exists = MatchEvent::where('match_id', $goal->match_id)
                ->where('player_id', $goal->player_id)
                ->where('event_type', 'goal')
                ->where('minute', $goal->minute)
                ->exists();

            if (!$exists) {
                $match = DB::table('matches')->find($goal->match_id);
                $teamId = null;
                if ($match) {
                    $player = DB::table('players')->find($goal->player_id);
                    if ($player) {
                        $teamId = $player->team_id;
                    }
                }

                MatchEvent::create([
                    'match_id' => $goal->match_id,
                    'team_id' => $teamId,
                    'player_id' => $goal->player_id,
                    'event_type' => 'goal',
                    'minute' => $goal->minute ?? 0,
                    'added_time' => 0,
                    'created_at' => $goal->created_at ?? now(),
                    'updated_at' => $goal->updated_at ?? now(),
                ]);
            }
        }

        Schema::dropIfExists('goals');
    }

    public function down(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->integer('minute')->nullable();
            $table->timestamps();
        });

        $goals = MatchEvent::goal()->get();
        foreach ($goals as $event) {
            DB::table('goals')->insert([
                'match_id' => $event->match_id,
                'player_id' => $event->player_id,
                'minute' => $event->minute,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
            ]);
        }
    }
};
