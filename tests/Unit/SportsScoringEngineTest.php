<?php

namespace Tests\Unit;

use App\Models\Competition;
use App\Models\Match_;
use App\Models\Team;
use App\Services\ScoringEngine;
use App\Services\SportsScoringEngine;
use App\Services\StandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SportsScoringEngineTest extends TestCase
{
    use RefreshDatabase;

    private SportsScoringEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new SportsScoringEngine(new StandingService(new ScoringEngine));
    }

    public function test_supports_match_basis(): void
    {
        $this->assertTrue($this->engine->supports('match'));
        $this->assertFalse($this->engine->supports('submission'));
    }

    public function test_calculate_ranking_delegates_to_standing_service(): void
    {
        $competition = Competition::factory()->create(['format' => Competition::FORMAT_LEAGUE]);
        $team1 = Team::factory()->create(['name' => 'Alpha']);
        $team2 = Team::factory()->create(['name' => 'Beta']);
        $competition->teams()->attach([$team1->id, $team2->id]);

        Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'score_team1' => 2,
            'score_team2' => 0,
            'status' => 'completed',
        ]);

        $ranking = $this->engine->calculateRanking($competition);

        $this->assertCount(2, $ranking);
        $this->assertEquals('Alpha', $ranking[0]['team_name']);
        $this->assertEquals(3, $ranking[0]['points']);
    }
}
