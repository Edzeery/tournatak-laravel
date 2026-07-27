<?php

use App\Models\User;

test('admin can access players index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin/players');
    $response->assertStatus(200);
});

test('admin can access matches index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin/matches');
    $response->assertStatus(200);
});

test('admin can access competitions index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin/competitions');
    $response->assertStatus(200);
});

test('admin can access users index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin/users');
    $response->assertStatus(200);
});

test('admin can access positions index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin/positions');
    $response->assertStatus(200);
});
