<?php

use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Models\User;
use Livewire\Livewire;

test('register page renders successfully', function () {
    $response = $this->get('/register');
    $response->assertOk();
});

test('can register new user', function () {
    Livewire::test(RegisterPage::class)
        ->set('name', 'Test User')
        ->set('username', 'testuser')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'user')
        ->call('register')
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'username' => 'testuser',
    ]);
});

test('registration fails with missing fields', function () {
    Livewire::test(RegisterPage::class)
        ->call('register')
        ->assertHasErrors(['name', 'email', 'password']);
});

test('registration fails with duplicate email', function () {
    User::factory()->create(['email' => 'existing@example.com', 'username' => 'existinguser']);

    Livewire::test(RegisterPage::class)
        ->set('name', 'Another User')
        ->set('username', 'testuser2')
        ->set('email', 'existing@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'user')
        ->call('register')
        ->assertHasErrors(['email']);
});

test('registration fails with password mismatch', function () {
    Livewire::test(RegisterPage::class)
        ->set('name', 'Test User')
        ->set('username', 'testuser')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'differentpass')
        ->set('role', 'user')
        ->call('register')
        ->assertHasErrors(['password']);
});

test('registered user can log in', function () {
    Livewire::test(RegisterPage::class)
        ->set('name', 'Test User')
        ->set('username', 'testuser')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'user')
        ->call('register')
        ->assertRedirect(route('login'));

    $user = User::where('email', 'test@example.com')->first();
    $user->markEmailAsVerified();
    $user->update(['is_verified' => true]);

    Livewire::test(LoginPage::class)
        ->set('identifier', 'test@example.com')
        ->set('password', 'password123')
        ->call('login');

    $this->assertAuthenticated();
});
