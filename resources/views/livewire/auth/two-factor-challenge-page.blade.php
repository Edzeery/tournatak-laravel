<div class="auth-wrapper">
    <div class="hero-shape hero-shape-auth-1 rtl-end"></div>
    <div class="hero-shape hero-shape-auth-2 rtl-left"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card">
                    <div class="auth-logo">
                        <i class="bi bi-shield-lock-fill text-dark"></i>
                    </div>
                    <h2>{{ __('app.two_factor_auth') }}</h2>
                    <p class="auth-subtitle">{{ __('app.two_factor_subtitle') }}</p>

                    <x-form-errors class="alert-dark-danger mb-4" />

                    <form wire:submit="verify">
                        <div class="mb-4">
                            <label class="form-label">{{ __('app.verification_code') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-key position-absolute text-theme-muted input-icon-pos"></i>
                                <input type="text" class="form-control tfa-code-input" placeholder="000000" wire:model="code" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autofocus required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold fs-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="verify">
                                <i class="bi bi-check-circle me-2"></i> {{ __('app.verify_button') }}
                            </span>
                            <span wire:loading wire:target="verify">
                                <span class="spinner-border spinner-border-sm me-2"></span> {{ __('app.logging_in') }}
                            </span>
                        </button>
                    </form>

                    <div class="auth-divider">{{ __('app.or') }}</div>

                    <div class="text-center">
                        <span class="text-theme-muted fs-md">
                            {{ __('app.use_recovery_code') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
