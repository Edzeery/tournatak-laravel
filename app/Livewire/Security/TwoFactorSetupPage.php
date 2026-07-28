<?php

namespace App\Livewire\Security;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\TwoFactorRecoveryCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Services\SecurityActivityLogger;

#[Layout('layouts.app')]
class TwoFactorSetupPage extends Component
{
    public bool $isEnabled = false;
    public string $password = '';
    public string $verificationCode = '';
    public string $qrCodeSvg = '';
    public string $secretKey = '';
    public bool $showRecoveryCodes = false;
    public array $recoveryCodes = [];
    public bool $showSetupForm = false;

    public function mount()
    {
        $user = auth()->user();
        $this->isEnabled = $user->securitySetting?->twofa_app ?? false;

        if ($this->isEnabled) {
            $this->loadRecoveryCodes();
        }
    }

    public function initiateSetup()
    {
        $this->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', __('app.wrong_password'));
            return;
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $this->secretKey = $secret;

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $this->qrCodeSvg = $writer->writeString($qrCodeUrl);

        $this->showSetupForm = true;
        $this->password = '';
    }

    public function confirmSetup()
    {
        $this->validate([
            'verificationCode' => 'required|string|size:6',
        ]);

        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey(
            $this->secretKey,
            $this->verificationCode,
            1
        );

        if (!$valid) {
            $this->addError('verificationCode', __('app.invalid_2fa_code'));
            return;
        }

        $user = auth()->user();
        $user->securitySetting->update([
            'twofa_app' => true,
            'twofa_app_secret' => $this->secretKey,
        ]);

        // Generate recovery codes
        $this->generateRecoveryCodes($user);

        $this->isEnabled = true;
        $this->showSetupForm = false;
        $this->showRecoveryCodes = true;
        $this->loadRecoveryCodes();
        $this->secretKey = '';

        SecurityActivityLogger::twoFactorEnabled($user);
        $this->dispatch('twofa-enabled');
    }

    public function disable2FA()
    {
        $this->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', __('app.wrong_password'));
            return;
        }

        $user->securitySetting->update([
            'twofa_app' => false,
            'twofa_app_secret' => null,
        ]);

        // Delete recovery codes
        $user->recoveryCodes()->delete();

        $this->isEnabled = false;
        $this->password = '';
        $this->showSetupForm = false;
        $this->recoveryCodes = [];

        SecurityActivityLogger::twoFactorDisabled($user);
        session()->flash('success', __('app.two_factor_disabled'));
    }

    public function generateNewRecoveryCodes()
    {
        $this->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', __('app.wrong_password'));
            return;
        }

        // Delete old unused codes
        $user->recoveryCodes()->whereNull('used_at')->delete();

        $this->generateRecoveryCodes($user);
        $this->loadRecoveryCodes();
        $this->showRecoveryCodes = true;
        $this->password = '';

        session()->flash('success', __('app.recovery_codes_generated'));
    }

    protected function generateRecoveryCodes($user)
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $code = strtoupper(Str::random(4) . '-' . Str::random(4));
            $codes[] = $code;

            TwoFactorRecoveryCode::create([
                'user_id' => $user->id,
                'code' => Hash::make($code),
            ]);
        }
        $this->recoveryCodes = $codes;
    }

    protected function loadRecoveryCodes()
    {
        $user = auth()->user();
        $this->recoveryCodes = $user->recoveryCodes()
            ->whereNull('used_at')
            ->pluck('code')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.security.two-factor-setup-page', [
            'title' => __('app.page_title_two_factor_setup'),
        ]);
    }
}
