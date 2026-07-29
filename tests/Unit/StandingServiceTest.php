<?php

namespace Tests\Unit;

use App\Models\Competition;
use App\Models\Match_;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Services\StandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandingServiceTest extends TestCase
{
    use RefreshDatabase;

    private StandingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StandingService();
    }

    public function test_calculate_standings_from_completed_matches(): void
    {
        $competition = Competition::factory()->create();
        $team1 = Team::factory()->create(['name' => 'Team A']);
        $team2 = Team::factory()->create(['name' => 'Team B']);
        $competition->teams()->attach([$team1->id, $team2->id]);

        Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'score_team1' => 3,
            'score_team2' => 1,
            'status' => 'completed',
        ]);

        $standings = $this->service->calculate($competition);

        $this->assertCount(2, $standings);
        $this->assertEquals('Team A', $standings[0]['team_name']);
        $this->assertEquals(3, $standings[0]['points']);
        $this->assertEquals(1, $standings[0]['played']);
        $this->assertEquals(1, $standings[0]['won']);
        $this->assertEquals(3, $standings[0]['goals_for']);
        $this->assertEquals(1, $standings[0]['goals_against']);
        $this->assertEquals(2, $standings[0]['goal_difference']);
    }

    public function test_standings_are_sorted_by_points(): void
    {
        $competition = Competition::factory()->create();
        $team1 = Team::factory()->create(['name' => 'Top Team']);
        $team2 = Team::factory()->create(['name' => 'Middle Team']);
        $team3 = Team::factory()->create(['name' => 'Bottom Team']);
        $competition->teams()->attach([$team1->id, $team2->id, $team3->id]);

        Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'score_team1' => 2,
            'score_team2' => 0,
            'status' => 'completed',
        ]);
        Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team3->id,
            'score_team1' => 1,
            'score_team2' => 1,
            'status' => 'completed',
        ]);
        Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team2->id,
            'team2_id' => $team3->id,
            'score_team1' => 0,
            'score_team2' => 2,
            'status' => 'completed',
        ]);

        $standings = $this->service->calculate($competition);

        $this->assertCount(3, $standings);
        $this->assertEquals('Top Team', $standings[0]['team_name']);
        $this->assertEquals('Bottom Team', $standings[1]['team_name']);
        $this->assertEquals('Middle Team', $standings[2]['team_name']);
        $this->assertSame(4, $standings[0]['points']);
        $this->assertSame(4, $standings[1]['points']);
        $this->assertSame(0, $standings[2]['points']);
    }

    public function test_goal_difference_tiebreaker(): void
    {
        $competition = Competition::factory()->create();
        $team1 = Team::factory()->create(['name' => 'Better GD']);
        $team2 = Team::factory()->create(['name' => 'Worse GD']);
        $competition->teams()->attach([$team1->id, $team2->id]);

        Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'score_team1' => 5,
            'score_team2' => 0,
            'status' => 'completed',
        ]);

        $standings = $this->service->calculate($competition);

        $this->assertEquals('Better GD', $standings[0]['team_name']);
        $this->assertEquals(5, $standings[0]['goal_difference']);
        $this->assertEquals(-5, $standings[1]['goal_difference']);
    }

    public function test_draw_adds_one_point_to_both_teams(): void
    {
        $competition = Competition::factory()->create();
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $competition->teams()->attach([$team1->id, $team2->id]);

        Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'score_team1' => 1,
            'score_team2' => 1,
            'status' => 'completed',
        ]);

        $standings = $this->service->calculate($competition);

        $this->assertEquals(1, $standings[0]['points']);
        $this->assertEquals(1, $standings[1]['points']);
        $this->assertEquals(1, $standings[0]['drawn']);
        $this->assertEquals(1, $standings[1]['drawn']);
    }

    public function test_get_top_scorers_returns_ordered_results(): void
    {
        $competition = Competition::factory()->create();
        $team = Team::factory()->create();
        $match = Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team->id,
            'team2_id' => Team::factory()->create()->id,
            'status' => 'completed',
        ]);
        $player1 = Player::factory()->create(['team_id' => $team->id]);
        $player2 = Player::factory()->create(['team_id' => $team->id]);

        MatchEvent::factory()->create([
            'match_id' => $match->id,
            'player_id' => $player1->id,
            'event_type' => 'goal',
            'minute' => 10,
        ]);
        MatchEvent::factory()->create([
            'match_id' => $match->id,
            'player_id' => $player1->id,
            'event_type' => 'goal',
            'minute' => 20,
        ]);
        MatchEvent::factory()->create([
            'match_id' => $match->id,
            'player_id' => $player2->id,
            'event_type' => 'goal',
            'minute' => 30,
        ]);

        $scorers = $this->service->getTopScorers($competition);

        $this->assertCount(2, $scorers);
        $this->assertEquals(2, $scorers[0]->total_goals);
        $this->assertEquals(1, $scorers[1]->total_goals);
    }

    public function test_get_assists_returns_empty_when_no_assists(): void
    {
        $competition = Competition::factory()->create();

        $assists = $this->service->getAssists($competition);

        $this->assertEmpty($assists);
    }

    public function test_get_yellow_cards_returns_ordered_results(): void
    {
        $competition = Competition::factory()->create();
        $team = Team::factory()->create();
        $match = Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team->id,
            'team2_id' => Team::factory()->create()->id,
            'status' => 'completed',
        ]);
        $player = Player::factory()->create(['team_id' => $team->id]);

        MatchEvent::factory()->create([
            'match_id' => $match->id,
            'player_id' => $player->id,
            'event_type' => 'yellow_card',
            'minute' => 15,
        ]);

        $cards = $this->service->getYellowCards($competition);

        $this->assertCount(1, $cards);
    }

    public function test_get_red_cards_returns_ordered_results(): void
    {
        $competition = Competition::factory()->create();
        $team = Team::factory()->create();
        $match = Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team->id,
            'team2_id' => Team::factory()->create()->id,
            'status' => 'completed',
        ]);
        $player = Player::factory()->create(['team_id' => $team->id]);

        MatchEvent::factory()->create([
            'match_id' => $match->id,
            'player_id' => $player->id,
            'event_type' => 'red_card',
            'minute' => 70,
        ]);

        $cards = $this->service->getRedCards($competition);

        $this->assertCount(1, $cards);
    }

    public function test_calculate_with_no_matches(): void
    {
        $competition = Competition::factory()->create();
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $competition->teams()->attach([$team1->id, $team2->id]);

        $standings = $this->service->calculate($competition);

        $this->assertCount(2, $standings);
        $this->assertEquals(0, $standings[0]['played']);
        $this->assertEquals(0, $standings[0]['points']);
    }

    public function test_form_tracks_recent_results(): void
    {
        $competition = Competition::factory()->create();
        $team1 = Team::factory()->create(['name' => 'Winner']);
        $team2 = Team::factory()->create(['name' => 'Loser']);
        $competition->teams()->attach([$team1->id, $team2->id]);

        Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'score_team1' => 3,
            'score_team2' => 0,
            'status' => 'completed',
        ]);

        $standings = $this->service->calculate($competition);

        $this->assertContains('W', $standings[0]['form']);
        $this->assertContains('L', $standings[1]['form']);
    }

    public function test_clean_sheets_tracked_correctly(): void
    {
        $competition = Competition::factory()->create();
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $competition->teams()->attach([$team1->id, $team2->id]);

        Match_::factory()->create([
            'competition_id' => $competition->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'score_team1' => 2,
            'score_team2' => 0,
            'status' => 'completed',
        ]);

        $standings = $this->service->calculate($competition);

        $this->assertEquals(1, $standings[0]['clean_sheets']);
        $this->assertEquals(0, $standings[1]['clean_sheets']);
    }
}
