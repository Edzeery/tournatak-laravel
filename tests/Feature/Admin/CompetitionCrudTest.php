<?php

use App\Models\Competition;
use App\Models\CompetitionSubtype;
use App\Models\CompetitionType;
use App\Models\User;

test('admin can access competitions index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/panel/competitions')->assertStatus(200);
});

test('admin can access create competition page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/panel/competitions/create')->assertStatus(200);
});

test('admin can access edit competition page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $competition = Competition::factory()->create();

    $this->actingAs($admin)->get("/panel/competitions/{$competition->id}/edit")->assertStatus(200);
});

test('guest cannot access admin competitions', function () {
    $this->get('/panel/competitions')->assertRedirect('/login');
});

test('non-admin cannot access admin competitions', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/panel/competitions')->assertForbidden();
});

test('admin can access competition types page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/panel/types')->assertStatus(200);
});

test('admin can access competition subtypes page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/panel/subtypes')->assertStatus(200);
});

test('admin can access create type page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/panel/types/create')->assertStatus(200);
});

test('admin can access create subtype page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/panel/subtypes/create')->assertStatus(200);
});

test('admin can access edit type page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $type = CompetitionType::factory()->create();

    $this->actingAs($admin)->get("/panel/types/{$type->id}/edit")->assertStatus(200);
});

test('admin can access edit subtype page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $subtype = CompetitionSubtype::factory()->create();

    $this->actingAs($admin)->get("/panel/subtypes/{$subtype->id}/edit")->assertStatus(200);
});
