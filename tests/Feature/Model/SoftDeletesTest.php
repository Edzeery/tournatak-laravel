<?php

use App\Models\Team;
use App\Models\Player;
use App\Models\Competition;
use App\Models\User;

test('team can be soft deleted and restored', function () {
    $team = Team::factory()->create();
    $team->delete();

    $this->assertSoftDeleted('teams', ['id' => $team->id]);

    Team::onlyTrashed()->where('id', $team->id)->restore();

    $this->assertDatabaseHas('teams', ['id' => $team->id, 'deleted_at' => null]);
});

test('player can be soft deleted', function () {
    $player = Player::factory()->create();
    $player->delete();

    $this->assertSoftDeleted('players', ['id' => $player->id]);
});

test('competition can be soft deleted', function () {
    $competition = Competition::factory()->create();
    $competition->delete();

    $this->assertSoftDeleted('competitions', ['id' => $competition->id]);
});

test('admin can access trash page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/panel/trash');
    $response->assertStatus(200);
});

test('admin can access security log page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/panel/security-log');
    $response->assertStatus(200);
});
