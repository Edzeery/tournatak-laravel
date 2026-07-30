<?php

namespace Tests\Unit;

use App\Models\Match_;
use App\Models\MatchLineup;
use App\Models\Player;
use App\Models\Team;
use App\Services\MatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchServiceTest extends TestCase
{
    use RefreshDatabase;

    private MatchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MatchService;
    }

    public function test_transition_to_first_half(): void
    {
        $match = Match_::factory()->create(['status' => 'scheduled']);

        $this->service->transitionPhase($match, Match_::PHASE_FIRST_HALF);

        $match->refresh();
        $this->assertEquals('in_progress', $match->status);
        $this->assertEquals('first_half', $match->extra_data['phase']);
        $this->assertNotNull($match->extra_data['first_half_started_at']);
    }

    public function test_transition_to_full_time(): void
    {
        $match = Match_::factory()->create(['status' => 'in_progress']);

        $this->service->transitionPhase($match, Match_::PHASE_FULL_TIME);

        $match->refresh();
        $this->assertEquals('completed', $match->status);
        $this->assertEquals('full_time', $match->extra_data['phase']);
    }

    public function test_update_score(): void
    {
        $match = Match_::factory()->create();

        $this->service->updateScore($match, 3, 1);

        $match->refresh();
        $this->assertEquals(3, $match->score_team1);
        $this->assertEquals(1, $match->score_team2);
    }

    public function test_add_event(): void
    {
        $match = Match_::factory()->create();
        $team = Team::factory()->create();

        $event = $this->service->addEvent($match, $team->id, 'goal', 'Test goal');

        $this->assertDatabaseHas('match_events', [
            'match_id' => $match->id,
            'team_id' => $team->id,
            'event_type' => 'goal',
            'description' => 'Test goal',
        ]);
    }

    public function test_add_event_with_override_minute(): void
    {
        $match = Match_::factory()->create();

        $event = $this->service->addEvent($match, 1, 'goal', 'test', null, 42);

        $this->assertEquals(42, $event->minute);
    }

    public function test_handle_goal_increments_score(): void
    {
        $match = Match_::factory()->create(['score_team1' => 0, 'score_team2' => 0]);

        $this->service->handleGoal($match, $match->team1_id, 'Goal!');

        $match->refresh();
        $this->assertEquals(1, $match->score_team1);
        $this->assertEquals(0, $match->score_team2);
    }

    public function test_handle_own_goal_increments_opponent_score(): void
    {
        $match = Match_::factory()->create(['score_team1' => 0, 'score_team2' => 0]);

        $this->service->handleOwnGoal($match, $match->team1_id, 'Own goal');

        $match->refresh();
        $this->assertEquals(0, $match->score_team1);
        $this->assertEquals(1, $match->score_team2);
    }

    public function test_save_added_time(): void
    {
        $match = Match_::factory()->create();

        $this->service->saveAddedTime($match, 2, 4, 1, 2);

        $match->refresh();
        $this->assertEquals(2, $match->added_time_first_half);
        $this->assertEquals(4, $match->added_time_second_half);
        $this->assertEquals(1, $match->extra_data['added_time_et_first_half']);
        $this->assertEquals(2, $match->extra_data['added_time_et_second_half']);
    }

    public function test_get_lineup_players(): void
    {
        $match = Match_::factory()->create();
        $player = Player::factory()->create(['team_id' => $match->team1_id]);
        MatchLineup::factory()->create([
            'match_id' => $match->id,
            'team_id' => $match->team1_id,
            'player_id' => $player->id,
        ]);

        $players = $this->service->getLineupPlayers($match, $match->team1_id);

        $this->assertCount(1, $players);
        $this->assertEquals($player->id, $players->first()->id);
    }
}
