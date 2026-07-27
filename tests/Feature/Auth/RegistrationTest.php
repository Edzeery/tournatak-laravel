<?php

use App\Livewire\Auth\RegisterPage;
use App\Models\User;
use Livewire\Livewire;

test('register page renders successfully', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

test('user can register with valid data', function () {
    Livewire::test(RegisterPage::class)
        ->set('name', 'Test User')
        ->set('username', 'testuser')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'viewer')
        ->call('register')
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'username' => 'testuser',
    ]);
});

test('user cannot register with duplicate email', function () {
    User::factory()->create(['email' => 'existing@example.com', 'username' => 'existinguser']);

    Livewire::test(RegisterPage::class)
        ->set('name', 'Test User')
        ->set('username', 'testuser2')
        ->set('email', 'existing@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'viewer')
        ->call('register')
        ->assertHasErrors(['email']);
});
