<div class="container py-4 container-page-sm">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1 tfa-section-title">
                <i class="bi bi-shield-lock-fill text-gold me-2"></i> {{ __('app.two_factor_auth') }}
            </h2>
            <p class="tfa-section-desc">{{ __('app.add_protection_desc') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4 tfa-alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <div class="fs-md">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 tfa-alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div class="fs-sm">{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Status Card --}}
    <div class="auth-card mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="tfa-status-icon {{ $isEnabled ? 'tfa-icon-enabled' : 'tfa-icon-disabled' }}">
                    <i class="bi bi-{{ $isEnabled ? 'shield-fill-check' : 'shield-x' }}"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold tfa-section-title">{{ $isEnabled ? __('app.two_factor_enabled_status') : __('app.two_factor_disabled_status') }}</h5>
                    <p class="tfa-recovery-desc">
                        {{ $isEnabled ? __('app.account_protected') : __('app.add_protection_desc') }}
                    </p>
                </div>
            </div>
            <span class="badge {{ $isEnabled ? 'tfa-badge-enabled' : 'tfa-badge-disabled' }}">
                {{ $isEnabled ? __('app.enabled') : __('app.disabled') }}
            </span>
        </div>
    </div>

    {{-- Enable 2FA --}}
    @if(!$isEnabled && !$showSetupForm)
        <div class="auth-card">
            <h5 class="fw-bold mb-3 tfa-section-title">
                <i class="bi bi-qr-code me-2 text-gold"></i> {{ __('app.enable_2fa') }}
            </h5>
            <p class="tfa-section-desc">
                {{ __('app.use_auth_app_desc') }}
            </p>

            <form wire:submit="initiateSetup">
                <div class="mb-3">
                    <label class="form-label">{{ __('app.enter_password_confirm') }}</label>
                    <div class="position-relative">
                        <i class="bi bi-lock position-absolute tfa-icon-lock"></i>
                        <input type="password" class="form-control tfa-input-icon" placeholder="••••••••" wire:model="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="initiateSetup">
                        <i class="bi bi-qr-code me-2"></i> {{ __('app.setup_2fa') }}
                    </span>
                    <span wire:loading wire:target="initiateSetup">
                        <span class="spinner-border spinner-border-sm me-2"></span> {{ __('app.preparing') }}
                    </span>
                </button>
            </form>
        </div>
    @endif

    {{-- Setup Form with QR Code --}}
    @if($showSetupForm)
        <div class="auth-card mb-4">
            <h5 class="fw-bold mb-3 tfa-section-title">
                <i class="bi bi-qr-code me-2 text-gold"></i> {{ __('app.scan_qr_code') }}
            </h5>
            <p class="tfa-section-desc">
                {{ __('app.scan_qr_desc') }}
            </p>

            <div class="text-center mb-4 p-4 tfa-qr-wrap">
                {!! $qrCodeSvg !!}
            </div>

            <div class="mb-4">
                <label class="form-label tfa-label">{{ __('app.or_enter_key_manually') }}</label>
                <div class="position-relative">
                    <input type="text" class="form-control tfa-secret-input" value="{{ $secretKey }}" readonly>
                </div>
            </div>

            <form wire:submit="confirmSetup">
                <div class="mb-3">
                    <label class="form-label">{{ __('app.enter_verification_code_app') }}</label>
                    <div class="position-relative">
                        <i class="bi bi-key position-absolute tfa-icon-lock"></i>
                        <input type="text" class="form-control tfa-input-icon tfa-code-input" placeholder="000000" wire:model="verificationCode" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmSetup">
                        <i class="bi bi-check-circle me-2"></i> {{ __('app.confirm_activation') }}
                    </span>
                    <span wire:loading wire:target="confirmSetup">
                        <span class="spinner-border spinner-border-sm me-2"></span> {{ __('app.logging_in') }}
                    </span>
                </button>
            </form>
        </div>
    @endif

    {{-- Recovery Codes --}}
    @if($isEnabled && count($recoveryCodes) > 0)
        <div class="auth-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0 tfa-section-title">
                    <i class="bi bi-key me-2 text-gold"></i> {{ __('app.recovery_codes') }}
                </h5>
                <button class="btn btn-sm tfa-btn-gold" wire:click="$toggle('showRecoveryCodes')">
                    <i class="bi bi-{{ $showRecoveryCodes ? 'eye-slash' : 'eye' }} me-1"></i>
                    {{ $showRecoveryCodes ? __('app.hide') : __('app.show') }}
                </button>
            </div>

            @if($showRecoveryCodes)
                <div class="p-3 mb-3 tfa-recovery-box">
                    <p class="tfa-recovery-desc">
                        {{ __('app.keep_codes_safe') }}
                    </p>
                    <div class="row g-2">
                        @foreach($recoveryCodes as $code)
                            <div class="col-6 col-md-4">
                                <div class="text-center p-2 tfa-recovery-code">
                                    {{ $code }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="tfa-recovery-count">{{ __('app.recovery_codes_count', ['count' => count($recoveryCodes)]) }}</p>
            @endif
        </div>
    @endif

    {{-- Disable 2FA / Regenerate Recovery Codes --}}
    @if($isEnabled)
        <div class="auth-card mb-4">
            <h5 class="fw-bold mb-3 tfa-section-title">
                <i class="bi bi-arrow-repeat me-2 text-gold"></i> {{ __('app.generate_new_recovery_codes') }}
            </h5>
            <form wire:submit="generateNewRecoveryCodes">
                <div class="mb-3">
                    <label class="form-label">{{ __('app.enter_password_confirm') }}</label>
                    <div class="position-relative">
                        <i class="bi bi-lock position-absolute tfa-icon-lock"></i>
                        <input type="password" class="form-control tfa-input-icon" placeholder="••••••••" wire:model="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-sm w-100 tfa-btn-gold" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="generateNewRecoveryCodes">
                        <i class="bi bi-arrow-repeat me-2"></i> {{ __('app.generate_new_codes') }}
                    </span>
                    <span wire:loading wire:target="generateNewRecoveryCodes">
                        <span class="spinner-border spinner-border-sm me-2"></span> {{ __('app.generating') }}
                    </span>
                </button>
            </form>
        </div>

        <div class="auth-card tfa-danger-card">
            <h5 class="fw-bold mb-3 tfa-danger-title">
                <i class="bi bi-exclamation-triangle me-2"></i> {{ __('app.disable_2fa') }}
            </h5>
            <p class="tfa-danger-desc">
                {{ __('app.disable_2fa_warning') }}
            </p>
            <form wire:submit="disable2FA">
                <div class="mb-3">
                    <label class="form-label">{{ __('app.enter_password_confirm') }}</label>
                    <div class="position-relative">
                        <i class="bi bi-lock position-absolute tfa-icon-lock"></i>
                        <input type="password" class="form-control tfa-input-icon" placeholder="••••••••" wire:model="password" required>
                    </div>
                </div>
                <button type="submit" class="btn w-100 tfa-btn-danger" wire:loading.attr="disabled" onclick="event.preventDefault(); confirmSweetAlert('{{ route('user.security.2fa') }}', '{{ addslashes(__('app.confirm_delete_title')) }}', '{{ addslashes(__('app.confirm_delete_message')) }}', '{{ addslashes(__('app.confirm_delete_yes')) }}', '{{ addslashes(__('app.confirm_delete_cancel')) }}'); this.closest('form').submit();">
                    <span wire:loading.remove wire:target="disable2FA">
                        <i class="bi bi-shield-x me-2"></i> {{ __('app.disable_2fa') }}
                    </span>
                    <span wire:loading wire:target="disable2FA">
                        <span class="spinner-border spinner-border-sm me-2"></span> {{ __('app.disabling') }}
                    </span>
                </button>
            </form>
        </div>
    @endif
</div>
