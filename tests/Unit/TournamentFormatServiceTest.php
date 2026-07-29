<?php

namespace Tests\Unit;

use App\Models\Competition;
use App\Models\Team;
use App\Services\TournamentFormatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentFormatServiceTest extends TestCase
{
    use RefreshDatabase;

    private TournamentFormatService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TournamentFormatService();
    }

    public function test_generate_league_creates_round_robin_matches(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_LEAGUE,
        ]);
        $teams = Team::factory()->count(4)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $matches = $this->service->generateLeague($competition);

        $this->assertCount(6, $matches);
        foreach ($matches as $match) {
            $this->assertEquals($competition->id, $match['competition_id']);
            $this->assertEquals('scheduled', $match['status']);
            $this->assertNotNull($match['team1_id']);
            $this->assertNotNull($match['team2_id']);
        }
    }

    public function test_generate_league_with_single_team_returns_empty(): void
    {
        $competition = Competition::factory()->create();
        $team = Team::factory()->create();
        $competition->teams()->attach($team);

        $matches = $this->service->generateLeague($competition);

        $this->assertEmpty($matches);
    }

    public function test_generate_knockout_creates_power_of_two_bracket(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_KNOCKOUT,
        ]);
        $teams = Team::factory()->count(8)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $matches = $this->service->generateKnockout($competition);

        $this->assertCount(7, $matches);
    }

    public function test_generate_knockout_with_byes(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_KNOCKOUT,
        ]);
        $teams = Team::factory()->count(5)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $matches = $this->service->generateKnockout($competition);

        $this->assertGreaterThan(0, count($matches));
    }

    public function test_generate_knockout_with_third_place(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_KNOCKOUT,
            'format_config' => ['third_place_match' => true],
        ]);
        $teams = Team::factory()->count(8)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $matches = $this->service->generateKnockout($competition);

        $this->assertCount(8, $matches);
    }

    public function test_generate_groups_creates_group_stage_and_knockout(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_GROUPS,
            'format_config' => ['group_size' => 4, 'advance_per_group' => 2],
        ]);
        $teams = Team::factory()->count(8)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $matches = $this->service->generateGroups($competition);

        $this->assertGreaterThan(0, count($matches));
        $groupMatches = array_filter($matches, fn($m) => ($m['stage'] ?? null) === 'group');
        $knockoutMatches = array_filter($matches, fn($m) => ($m['stage'] ?? null) === 'knockout');
        $this->assertNotEmpty($groupMatches);
        $this->assertNotEmpty($knockoutMatches);
    }

    public function test_generate_home_away_creates_two_legs(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_HOME_AWAY,
        ]);
        $teams = Team::factory()->count(4)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $matches = $this->service->generateHomeAway($competition);

        $this->assertCount(12, $matches);
        $legs = array_count_values(array_column($matches, 'leg'));
        $this->assertEquals(6, $legs[1]);
        $this->assertEquals(6, $legs[2]);
    }

    public function test_generate_double_elimination_creates_winners_losers_and_grand_final(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_DOUBLE_ELIMINATION,
        ]);
        $teams = Team::factory()->count(8)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $matches = $this->service->generateDoubleElimination($competition);

        $brackets = array_count_values(array_column($matches, 'bracket'));
        $this->assertArrayHasKey('winners', $brackets);
        $this->assertArrayHasKey('losers', $brackets);
        $this->assertArrayHasKey('grand_final', $brackets);
    }

    public function test_generate_swiss_creates_specified_rounds(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_SWISS,
            'format_config' => ['swiss_rounds' => 5],
        ]);
        $teams = Team::factory()->count(8)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $matches = $this->service->generateSwiss($competition);

        $rounds = max(array_column($matches, 'round'));
        $this->assertEquals(5, $rounds);
    }

    public function test_generate_matches_delegates_to_correct_method(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_LEAGUE,
        ]);
        $teams = Team::factory()->count(4)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $matches = $this->service->generateMatches($competition);

        $this->assertCount(6, $matches);
    }

    public function test_get_format_config_merges_with_defaults(): void
    {
        $competition = Competition::factory()->create([
            'format_config' => ['group_size' => 6],
        ]);

        $config = $this->service->getFormatConfig($competition);

        $this->assertEquals(6, $config['group_size']);
        $this->assertEquals(1, $config['rounds']);
        $this->assertEquals(7, $config['swiss_rounds']);
    }

    public function test_create_match_persists_to_database(): void
    {
        $competition = Competition::factory()->create([
            'format' => Competition::FORMAT_LEAGUE,
        ]);
        $teams = Team::factory()->count(4)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $count = $this->service->createMatches($competition);

        $this->assertEquals(6, $count);
        $this->assertDatabaseCount('matches', 6);
    }

    public function test_get_format_config_uses_empty_array_when_null(): void
    {
        $competition = Competition::factory()->create([
            'format_config' => null,
        ]);

        $config = $this->service->getFormatConfig($competition);

        $this->assertEquals(4, $config['group_size']);
    }
}
