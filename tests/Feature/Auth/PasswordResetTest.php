<?php

use App\Livewire\Auth\ForgotPasswordPage;
use Livewire\Livewire;

test('forgot password page renders', function () {
    $response = $this->get('/forgot-password');
    $response->assertStatus(200);
});

test('password reset request sends email', function () {
    \App\Models\User::factory()->create(['email' => 'test@example.com']);

    Livewire::test(ForgotPasswordPage::class)
        ->set('email', 'test@example.com')
        ->call('sendResetLink')
        ->assertHasNoErrors();
});

test('password reset page renders with valid token', function () {
    $user = \App\Models\User::factory()->create();
    $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);

    $response = $this->get("/reset-password/{$token}");
    $response->assertStatus(200);
});
