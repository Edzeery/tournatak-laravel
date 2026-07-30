<?php

namespace Tests\Unit;

use App\Models\Competition;
use App\Services\ScoringEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringEngineTest extends TestCase
{
    use RefreshDatabase;

    private ScoringEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new ScoringEngine;
    }

    public function test_default_scoring_config(): void
    {
        $competition = Competition::factory()->create();

        $config = $this->engine->getConfig($competition);

        $this->assertEquals(3, $config['win']);
        $this->assertEquals(1, $config['draw']);
        $this->assertEquals(0, $config['loss']);
    }

    public function test_custom_scoring_from_format_config(): void
    {
        $competition = Competition::factory()->create([
            'format_config' => ['scoring' => ['win' => 2, 'draw' => 1, 'loss' => 0]],
        ]);

        $config = $this->engine->getConfig($competition);

        $this->assertEquals(2, $config['win']);
        $this->assertEquals(1, $config['draw']);
        $this->assertEquals(0, $config['loss']);
    }

    public function test_calculate_win_points(): void
    {
        $competition = Competition::factory()->create();

        $points = $this->engine->calculatePoints($competition, 3, 1);

        $this->assertEquals(3, $points);
    }

    public function test_calculate_draw_points(): void
    {
        $competition = Competition::factory()->create();

        $points = $this->engine->calculatePoints($competition, 2, 2);

        $this->assertEquals(1, $points);
    }

    public function test_calculate_loss_points(): void
    {
        $competition = Competition::factory()->create();

        $points = $this->engine->calculatePoints($competition, 0, 2);

        $this->assertEquals(0, $points);
    }

    public function test_custom_scoring_win_points(): void
    {
        $competition = Competition::factory()->create([
            'format_config' => ['scoring' => ['win' => 5, 'draw' => 2, 'loss' => 0]],
        ]);

        $this->assertEquals(5, $this->engine->calculatePoints($competition, 1, 0));
        $this->assertEquals(2, $this->engine->calculatePoints($competition, 0, 0));
        $this->assertEquals(0, $this->engine->calculatePoints($competition, 0, 1));
    }

    public function test_default_tiebreakers(): void
    {
        $competition = Competition::factory()->create();

        $tiebreakers = $this->engine->getTiebreakers($competition);

        $this->assertEquals(['goal_difference', 'goals_for', 'head_to_head'], $tiebreakers);
    }

    public function test_sort_standings_by_points(): void
    {
        $competition = Competition::factory()->create();
        $standings = [
            ['team_id' => 1, 'points' => 3, 'goals_for' => 2, 'goals_against' => 1],
            ['team_id' => 2, 'points' => 6, 'goals_for' => 5, 'goals_against' => 2],
            ['team_id' => 3, 'points' => 0, 'goals_for' => 1, 'goals_against' => 5],
        ];

        $sorted = $this->engine->sortStandings($standings, $competition);

        $this->assertEquals(2, $sorted[0]['team_id']);
        $this->assertEquals(1, $sorted[1]['team_id']);
        $this->assertEquals(3, $sorted[2]['team_id']);
    }

    public function test_sort_standings_by_goal_difference(): void
    {
        $competition = Competition::factory()->create();
        $standings = [
            ['team_id' => 1, 'points' => 3, 'goals_for' => 2, 'goals_against' => 1],
            ['team_id' => 2, 'points' => 3, 'goals_for' => 3, 'goals_against' => 3],
        ];

        $sorted = $this->engine->sortStandings($standings, $competition);

        $this->assertEquals(1, $sorted[0]['team_id']);
        $this->assertEquals(2, $sorted[1]['team_id']);
    }

    public function test_custom_tiebreakers_goals_for(): void
    {
        $competition = Competition::factory()->create([
            'format_config' => ['scoring' => ['tiebreakers' => ['goals_for', 'goal_difference']]],
        ]);
        $standings = [
            ['team_id' => 1, 'points' => 3, 'goals_for' => 2, 'goals_against' => 1],
            ['team_id' => 2, 'points' => 3, 'goals_for' => 5, 'goals_against' => 4],
        ];

        $sorted = $this->engine->sortStandings($standings, $competition);

        $this->assertEquals(2, $sorted[0]['team_id']);
        $this->assertEquals(1, $sorted[1]['team_id']);
    }
}
