<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\Notifies;
use App\Services\SecurityActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class LoginPage extends Component
{
    use Notifies;

    public string $identifier = '';

    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        // Rate limit: 5 attempts per minute per IP
        $throttleKey = 'login:'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->notify('error', __('app.rate_limit_exceeded', ['seconds' => $seconds]));

            return;
        }

        $credentials = filter_var($this->identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $this->identifier, 'password' => $this->password]
            : ['username' => $this->identifier, 'password' => $this->password];

        if (Auth::attempt($credentials, $this->remember)) {
            $user = Auth::user();

            if (! $user->is_verified) {
                Auth::logout();

                return redirect()->route('home')->with('error', __('app.activate_account_first'));
            }

            SecurityActivityLogger::login($user);

            // Check if 2FA is enabled
            if ($user->securitySetting?->twofa_app) {
                Auth::logout();
                session()->put('2fa_user_id', $user->id);

                return redirect()->route('2fa.challenge');
            }

            session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))->with('success', __('app.welcome_back').' '.$user->name);
        }

        SecurityActivityLogger::failedLogin($this->identifier);
        RateLimiter::hit($throttleKey, 60);

        return back()->withInput()->withErrors(['identifier' => __('app.invalid_credentials')]);
    }

    public function render()
    {
        return view('livewire.auth.login-page', [
            'title' => __('app.page_title_login'),
        ]);
    }
}
