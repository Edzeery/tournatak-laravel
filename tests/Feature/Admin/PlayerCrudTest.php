<?php

use App\Models\User;
use App\Models\Player;
use App\Models\Team;

test('admin can access players index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin/players')->assertStatus(200);
});

test('guest cannot access admin players', function () {
    $this->get('/admin/players')->assertRedirect('/login');
});

test('non-admin cannot access admin players', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/admin/players')->assertForbidden();
});

test('admin can access create player page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin/players/create')->assertStatus(200);
});

test('admin can access edit player page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team = Team::factory()->create();
    $player = Player::factory()->create(['team_id' => $team->id, 'date_of_birth' => '2000-01-15']);

    $this->actingAs($admin)->get("/admin/players/{$player->id}/edit")->assertStatus(200);
});

test('admin can soft delete a player via DB', function () {
    $player = Player::factory()->create();

    $player->delete();

    $this->assertSoftDeleted('players', ['id' => $player->id]);
});

test('admin can access player teams staff page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team = Team::factory()->create();

    $this->actingAs($admin)->get("/admin/teams/{$team->id}/staff")->assertStatus(200);
});

test('admin can access player medical page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team = Team::factory()->create();

    $this->actingAs($admin)->get("/admin/teams/{$team->id}/medical")->assertStatus(200);
});

test('admin can access player formations page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team = Team::factory()->create();

    $this->actingAs($admin)->get("/admin/teams/{$team->id}/formations")->assertStatus(200);
});

test('admin can access player tactics page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team = Team::factory()->create();

    $this->actingAs($admin)->get("/admin/teams/{$team->id}/tactics")->assertStatus(200);
});

test('admin can access player stats page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $team = Team::factory()->create();

    $this->actingAs($admin)->get("/admin/teams/{$team->id}/stats")->assertStatus(200);
});
