<?php

use App\Models\Competition;
use App\Models\Match_;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;

uses(\Tests\TestCase::class);

beforeEach(function () {
    $this->competition = Competition::factory()->create();
    $this->team1 = Team::factory()->create();
    $this->team2 = Team::factory()->create();
    $this->player = Player::factory()->create(['team_id' => $this->team1->id]);
    $this->user = User::factory()->create();
});

test('team soft delete works', function () {
    $this->team1->delete();

    $this->assertSoftDeleted($this->team1);
    $this->assertDatabaseHas('teams', ['id' => $this->team1->id, 'deleted_at' => now()]);
});

test('team soft delete preserves related players', function () {
    $playerId = $this->player->id;
    $this->team1->delete();

    $this->assertSoftDeleted($this->team1);
    $this->assertDatabaseHas('players', ['id' => $playerId]);
});

test('player belongs to team', function () {
    expect($this->player->team)->toBeInstanceOf(Team::class);
    expect($this->player->team->id)->toBe($this->team1->id);
});

test('team has many players', function () {
    Player::factory()->count(2)->create(['team_id' => $this->team1->id]);

    expect($this->team1->players)->toHaveCount(3);
});

test('competition has many matches', function () {
    Match_::factory()->count(3)->create([
        'competition_id' => $this->competition->id,
        'team1_id' => $this->team1->id,
        'team2_id' => $this->team2->id,
    ]);

    expect($this->competition->matches)->toHaveCount(3);
});

test('match belongs to competition', function () {
    $match = Match_::factory()->create([
        'competition_id' => $this->competition->id,
        'team1_id' => $this->team1->id,
        'team2_id' => $this->team2->id,
    ]);

    expect($match->competition)->toBeInstanceOf(Competition::class);
});

test('match has many events', function () {
    $match = Match_::factory()->create([
        'competition_id' => $this->competition->id,
        'team1_id' => $this->team1->id,
        'team2_id' => $this->team2->id,
    ]);
    MatchEvent::factory()->count(3)->create([
        'match_id' => $match->id,
        'team_id' => $this->team1->id,
        'player_id' => $this->player->id,
    ]);

    expect($match->events)->toHaveCount(3);
});

test('match event belongs to match', function () {
    $match = Match_::factory()->create([
        'competition_id' => $this->competition->id,
        'team1_id' => $this->team1->id,
        'team2_id' => $this->team2->id,
    ]);
    $event = MatchEvent::factory()->create([
        'match_id' => $match->id,
        'team_id' => $this->team1->id,
        'player_id' => $this->player->id,
    ]);

    expect($event->match)->toBeInstanceOf(Match_::class);
});

test('player name accessor returns user name', function () {
    $user = User::factory()->create(['name' => 'Test Player Name']);
    $player = Player::factory()->create([
        'team_id' => $this->team1->id,
        'user_id' => $user->id,
    ]);

    expect($player->name)->toBe('Test Player Name');
});

test('match_ has status constants', function () {
    expect(Match_::STATUS_SCHEDULED)->toBe('scheduled');
    expect(Match_::STATUS_IN_PROGRESS)->toBe('in_progress');
    expect(Match_::STATUS_COMPLETED)->toBe('completed');
    expect(Match_::STATUS_ABANDONED)->toBe('abandoned');
    expect(Match_::STATUS_POSTPONED)->toBe('postponed');
    expect(Match_::STATUS_CANCELLED)->toBe('cancelled');
});

test('competition has format constants', function () {
    expect(Competition::FORMAT_LEAGUE)->toBe('league');
    expect(Competition::FORMAT_KNOCKOUT)->toBe('knockout');
    expect(Competition::FORMAT_GROUPS)->toBe('groups');
    expect(Competition::FORMAT_HOME_AWAY)->toBe('home_away');
    expect(Competition::FORMAT_DOUBLE_ELIMINATION)->toBe('double_elimination');
    expect(Competition::FORMAT_SWISS)->toBe('swiss');
});

test('user has api tokens trait', function () {
    expect(in_array('Laravel\Sanctum\HasApiTokens', class_uses(User::class)))->toBeTrue();
});

test('team has soft deletes trait', function () {
    expect(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Team::class)))->toBeTrue();
});

test('player has soft deletes trait', function () {
    expect(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Player::class)))->toBeTrue();
});

test('competition has soft deletes trait', function () {
    expect(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Competition::class)))->toBeTrue();
});
