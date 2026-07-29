<?php

use App\Models\Competition;
use App\Models\Team;
use App\Models\Match_;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\User;

test('competition detail page renders', function () {
    $competition = Competition::factory()->create(['approval_status' => 'approved']);

    $response = $this->get(route('competitions.show', $competition));

    $response->assertOk();
    $response->assertSee($competition->name);
});

test('competition detail shows teams', function () {
    $competition = Competition::factory()->create(['approval_status' => 'approved']);
    $team = Team::factory()->create();
    $competition->teams()->attach($team->id);

    $response = $this->get(route('competitions.show', $competition));

    $response->assertOk();
    $response->assertSee($team->name);
});

test('competition detail shows matches', function () {
    $competition = Competition::factory()->create(['approval_status' => 'approved']);
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $match = Match_::factory()->create([
        'competition_id' => $competition->id,
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
        'status' => 'completed',
        'score_team1' => 2,
        'score_team2' => 1,
    ]);

    $response = $this->get(route('competitions.show', $competition));

    $response->assertOk();
    $response->assertSee($team1->name);
    $response->assertSee($team2->name);
});

test('competition detail shows standings for league format', function () {
    $competition = Competition::factory()->create([
        'approval_status' => 'approved',
        'format' => 'league',
    ]);
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $competition->teams()->attach([$team1->id, $team2->id]);
    Match_::factory()->create([
        'competition_id' => $competition->id,
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
        'status' => 'completed',
        'score_team1' => 1,
        'score_team2' => 0,
    ]);

    $response = $this->get(route('competitions.show', $competition));

    $response->assertOk();
});

test('competition detail 404 for missing competition', function () {
    $response = $this->get('/competitions/99999');
    $response->assertNotFound();
});
