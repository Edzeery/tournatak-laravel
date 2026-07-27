<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

#[Layout('layouts.app')]
class ForgotPasswordPage extends Component
{
    public string $email = '';

    public function sendResetLink()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        // Rate limit: 3 attempts per minute per email
        $throttleKey = 'password.reset:' . strtolower($this->email);

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            session()->flash('error', __('app.rate_limit_exceeded', ['seconds' => $seconds]));
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $user = User::where('email', $this->email)->first();

        if ($user) {
            $status = Password::broker()->sendResetLink(
                ['email' => $this->email]
            );

            if ($status === Password::RESET_LINK_SENT) {
                session()->flash('success', __('app.reset_link_sent'));
                return;
            }
        }

        // Always show success to prevent email enumeration
        session()->flash('success', __('app.reset_link_if_registered'));
    }

    public function render()
    {
        return view('livewire.auth.forgot-password-page', [
            'title' => __('app.page_title_forgot_password'),
        ]);
    }
}
