<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;
use App\Services\SecurityActivityLogger;

#[Layout('layouts.app')]
class TwoFactorChallengePage extends Component
{
    public string $code = '';

    public function mount()
    {
        if (!Session::has('2fa_user_id')) {
            return redirect()->route('login');
        }
    }

    public function verify()
    {
        $throttleKey = '2fa:' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('code', __('app.rate_limit_exceeded', ['seconds' => $seconds]));
            return;
        }

        $this->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = Session::get('2fa_user_id');
        $user = \App\Models\User::find($userId);

        if (!$user || !$user->securitySetting) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey(
            $user->securitySetting->twofa_app_secret,
            $this->code,
            1  // tolerance: 1 window (30 seconds)
        );

        if ($valid) {
            SecurityActivityLogger::twoFactorChallengePassed($user);
            auth()->login($user, true);
            Session::forget('2fa_user_id');
            session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        // Check recovery codes (hashed comparison)
        $recoveryCodes = $user->recoveryCodes()
            ->whereNull('used_at')
            ->get();

        $recoveryCode = $recoveryCodes->first(fn($rc) => Hash::check($this->code, $rc->code));

        if ($recoveryCode) {
            $recoveryCode->markAsUsed();
            SecurityActivityLogger::recoveryCodeUsed($user);
            auth()->login($user, true);
            Session::forget('2fa_user_id');
            session()->regenerate();

            session()->flash('warning', __('app.recovery_code_used'));
            return redirect()->intended(route('admin.dashboard'));
        }

        $this->addError('code', __('app.invalid_code'));
        RateLimiter::hit($throttleKey, 60);
    }

    public function render()
    {
        return view('livewire.auth.two-factor-challenge-page', [
            'title' => __('app.page_title_two_factor_auth'),
        ]);
    }
}
