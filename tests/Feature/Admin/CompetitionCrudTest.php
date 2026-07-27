<?php

use App\Models\User;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\CompetitionSubtype;

test('admin can access competitions index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin/competitions')->assertStatus(200);
});

test('admin can access create competition page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin/competitions/create')->assertStatus(200);
});

test('admin can access edit competition page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $competition = Competition::factory()->create();

    $this->actingAs($admin)->get("/admin/competitions/{$competition->id}/edit")->assertStatus(200);
});

test('guest cannot access admin competitions', function () {
    $this->get('/admin/competitions')->assertRedirect('/login');
});

test('non-admin cannot access admin competitions', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/admin/competitions')->assertForbidden();
});

test('admin can access competition types page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin/types')->assertStatus(200);
});

test('admin can access competition subtypes page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin/subtypes')->assertStatus(200);
});

test('admin can access create type page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin/types/create')->assertStatus(200);
});

test('admin can access create subtype page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin/subtypes/create')->assertStatus(200);
});

test('admin can access edit type page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $type = CompetitionType::factory()->create();

    $this->actingAs($admin)->get("/admin/types/{$type->id}/edit")->assertStatus(200);
});

test('admin can access edit subtype page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $subtype = CompetitionSubtype::factory()->create();

    $this->actingAs($admin)->get("/admin/subtypes/{$subtype->id}/edit")->assertStatus(200);
});
