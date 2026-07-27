<?php

use App\Models\User;

test('admin can access teams index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin/teams');
    $response->assertStatus(200);
});

test('guest cannot access admin teams', function () {
    $response = $this->get('/admin/teams');
    $response->assertRedirect('/login');
});

test('non-admin user cannot access admin teams', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $response = $this->actingAs($user)->get('/admin/teams');
    $response->assertForbidden();
});
