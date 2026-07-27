<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;
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

        // Check recovery codes
        $recoveryCode = $user->recoveryCodes()
            ->where('code', $this->code)
            ->whereNull('used_at')
            ->first();

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
    }

    public function render()
    {
        return view('livewire.auth.two-factor-challenge-page', [
            'title' => __('app.page_title_two_factor_auth'),
        ]);
    }
}
