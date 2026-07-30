<?php

use App\Models\Match_;
use App\Models\Team;
use App\Models\User;

test('admin can access matches index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/panel/matches')->assertStatus(200);
});

test('admin can access create match page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/panel/matches/create')->assertStatus(200);
});

test('admin can access edit match page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $match = Match_::factory()->create([
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
    ]);

    $this->actingAs($admin)->get("/panel/matches/{$match->id}/edit")->assertStatus(200);
});

test('admin can access match lineup page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $match = Match_::factory()->create([
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
    ]);

    $this->actingAs($admin)->get("/panel/matches/{$match->id}/lineup")->assertStatus(200);
});

test('admin can access match stats page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $match = Match_::factory()->create([
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
    ]);

    $this->actingAs($admin)->get("/panel/matches/{$match->id}/stats")->assertStatus(200);
});

test('admin can access match events page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $match = Match_::factory()->create([
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
    ]);

    $this->actingAs($admin)->get("/panel/matches/{$match->id}/events")->assertStatus(200);
});

test('guest cannot access admin matches', function () {
    $this->get('/panel/matches')->assertRedirect('/login');
});

test('non-admin cannot access admin matches', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/panel/matches')->assertForbidden();
});
