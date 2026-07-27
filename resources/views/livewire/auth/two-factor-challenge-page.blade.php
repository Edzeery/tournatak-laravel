<div class="auth-wrapper">
    <div class="hero-shape" style="width:600px;height:600px;top:-300px;right:-200px;"></div>
    <div class="hero-shape" style="width:400px;height:400px;bottom:-200px;left:-150px;"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card">
                    <div class="auth-logo">
                        <i class="bi bi-shield-lock-fill text-dark"></i>
                    </div>
                    <h2>{{ __('app.two_factor_auth') }}</h2>
                    <p class="auth-subtitle">{{ __('app.two_factor_subtitle') }}</p>

                    @if($errors->any())
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 alert-dark-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <div class="fs-base">{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form wire:submit="verify">
                        <div class="mb-4">
                            <label class="form-label">{{ __('app.verification_code') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-key position-absolute text-theme-muted" style="right:14px;top:50%;transform:translateY(-50%);"></i>
                                <input type="text" class="form-control" style="padding-inline-end:42px;letter-spacing:8px;text-align:center;font-size:1.4rem;" placeholder="000000" wire:model="code" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autofocus required>
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
