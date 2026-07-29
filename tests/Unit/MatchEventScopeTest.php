<?php

use App\Models\MatchEvent;
use App\Models\Match_;
use App\Models\Player;
use App\Models\Team;

uses(\Tests\TestCase::class);

beforeEach(function () {
    $this->team1 = Team::factory()->create();
    $this->team2 = Team::factory()->create();
    $this->match = Match_::factory()->create([
        'team1_id' => $this->team1->id,
        'team2_id' => $this->team2->id,
    ]);
    $this->player = Player::factory()->create(['team_id' => $this->team1->id]);
});

test('scopeGoal returns goal own_goal and penalty_scored events', function () {
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'goal', 'minute' => 10]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'own_goal', 'minute' => 20]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'penalty_scored', 'minute' => 30]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'yellow_card', 'minute' => 40]);

    $goals = MatchEvent::goal()->get();

    expect($goals)->toHaveCount(3);
});

test('scopeScored returns goal and penalty_scored only', function () {
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'goal', 'minute' => 10]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'own_goal', 'minute' => 20]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'penalty_scored', 'minute' => 30]);

    $scored = MatchEvent::scored()->get();

    expect($scored)->toHaveCount(2);
});

test('scopeYellowCard returns yellow and second_yellow', function () {
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'yellow_card', 'minute' => 10]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'second_yellow', 'minute' => 20]);

    $cards = MatchEvent::yellowCard()->get();

    expect($cards)->toHaveCount(2);
});

test('scopeRedCard returns red_card only', function () {
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'red_card', 'minute' => 10]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'goal', 'minute' => 20]);

    $cards = MatchEvent::redCard()->get();

    expect($cards)->toHaveCount(1);
});

test('scopeAssist returns assist events', function () {
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'assist', 'minute' => 10]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'goal', 'minute' => 20]);

    $assists = MatchEvent::assist()->get();

    expect($assists)->toHaveCount(1);
});

test('player goals relationship uses scopeGoal', function () {
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'goal', 'minute' => 10]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'own_goal', 'minute' => 20]);
    MatchEvent::factory()->create(['match_id' => $this->match->id, 'team_id' => $this->team1->id, 'player_id' => $this->player->id, 'event_type' => 'yellow_card', 'minute' => 30]);

    expect($this->player->goals)->toHaveCount(2);
});
