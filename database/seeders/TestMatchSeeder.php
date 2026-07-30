<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestMatchSeeder extends Seeder
{
    public function run(): void
    {
        $team1 = Team::first();
        $team2 = Team::skip(1)->first();

        if (! $team1 || ! $team2) {
            $this->command->error('Need at least 2 teams!');

            return;
        }

        $subtypeId = DB::table('competition_subtypes')->pluck('id')->first();
        $typeId = DB::table('competition_types')->pluck('id')->first();

        if (! $subtypeId || ! $typeId) {
            DB::table('competition_subtypes')->insert(['name' => 'دوري', 'en_name' => 'League', 'created_at' => now(), 'updated_at' => now()]);
            $subtypeId = DB::table('competition_subtypes')->latest()->first()->id;
            DB::table('competition_types')->insert(['subtype_id' => $subtypeId, 'name' => 'دوري المحترفين', 'slug' => 'pro-league', 'sort_order' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
            $typeId = DB::table('competition_types')->latest()->first()->id;
        }

        DB::table('competitions')->insert([
            'name' => 'بطولة التجريبي',
            'type_id' => $typeId,
            'subtype_id' => $subtypeId,
            'organizer_id' => 1,
            'status' => 'ongoing',
            'approval_status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $competitionId = DB::table('competitions')->latest()->first()->id;

        DB::table('matches')->insertOrIgnore([
            'id' => 1,
            'competition_id' => $competitionId,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'match_date' => now()->addDays(5),
            'status' => 'scheduled',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Test match created! ID: 1');
    }
}
