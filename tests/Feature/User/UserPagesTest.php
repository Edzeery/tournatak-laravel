<?php

use App\Models\User;

test('authenticated user can access user dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/user/dashboard')->assertStatus(200);
});

test('authenticated user can access user profile', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/user/profile')->assertStatus(200);
});

test('authenticated user can access user security page', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/user/security')->assertStatus(200);
});

test('authenticated user can access notifications page', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/user/notifications')->assertStatus(200);
});

test('profile alias redirects to user profile', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/profile')->assertRedirect(route('user.profile'));
});

test('guest is redirected from user dashboard to login', function () {
    $this->get('/user/dashboard')->assertRedirect('/login');
});

test('guest is redirected from user profile to login', function () {
    $this->get('/user/profile')->assertRedirect('/login');
});

test('guest is redirected from user security to login', function () {
    $this->get('/user/security')->assertRedirect('/login');
});

test('guest is redirected from notifications to login', function () {
    $this->get('/user/notifications')->assertRedirect('/login');
});

test('authenticated user can access registrations page', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)->get('/user/registrations')->assertStatus(200);
});

test('guest is redirected from registrations page to login', function () {
    $this->get('/user/registrations')->assertRedirect('/login');
});
