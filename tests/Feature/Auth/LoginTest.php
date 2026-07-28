<?php

use App\Livewire\Auth\LoginPage;
use Livewire\Livewire;

test('login page renders successfully', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('user can login with valid credentials', function () {
    $user = \App\Models\User::factory()->create([
        'password' => bcrypt('password'),
        'is_verified' => true,
    ]);

    Livewire::test(LoginPage::class)
        ->set('identifier', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect();

    $this->assertAuthenticatedAs($user);
});

test('user cannot login with invalid credentials', function () {
    $user = \App\Models\User::factory()->create([
        'password' => bcrypt('password'),
        'is_verified' => true,
    ]);

    Livewire::test(LoginPage::class)
        ->set('identifier', $user->email)
        ->set('password', 'wrong-password')
        ->call('login');

    $this->assertGuest();
});

test('unauthenticated user is redirected to login from admin', function () {
    $response = $this->get('/panel/dashboard');
    $response->assertRedirect('/login');
});
