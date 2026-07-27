<div class="container py-4 container-page-md">
    <div class="mb-4">
        <h2 class="fw-bold text-theme-primary">
            <i class="bi bi-shield-fill-check text-gold me-2"></i> {{ __('app.security') }}
        </h2>
        <p class="text-theme-muted fs-md">{{ __('app.security_desc') }}</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="auth-card text-center p-4">
                <div class="d-flex align-items-center justify-content-center rounded-xl w-110 h-110 bg-gold-subtle mx-auto mb-3">
                    <i class="bi bi-shield-lock fs-2xl text-gold"></i>
                </div>
                <h5 class="fw-bold text-theme-primary">{{ __('app.two_factor_auth') }}</h5>
                <p class="text-theme-muted fs-base">{{ __('app.two_factor_desc') }}</p>
                <a href="{{ route('user.2fa-setup') }}" class="btn btn-sm mt-2 btn-gold-outline">
                    <i class="bi bi-gear me-1"></i> {{ __('app.setup') }}
                </a>
            </div>
        </div>
    </div>
</div>
