<div class="auth-wrapper">
    {{-- Background Shapes --}}
    <div class="hero-shape hero-shape-auth-1 rtl-end"></div>
    <div class="hero-shape hero-shape-auth-2 rtl-left"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center">
            {{-- Left side: branding --}}
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center px-5 pos-rel-z2">
                <a href="{{ route('home') }}" class="text-decoration-none mb-4">
                    <span class="text-gold fw-bold fs-18">
                        <img src="{{ asset('img/icons/icon.png') }}" alt="Bracketa" width="35" height="35"> {{ config('app.name') }}
                    </span>
                </a>
                <h1 class="text-white fw-bold mb-3 auth-hero-title">
                    {{ __('app.welcome_back') }}<br>
                    <span class="text-gold">{{ __('app.follow_your_journey') }}</span>
                </h1>
                <p class="auth-hero-desc">
                    {{ __('app.welcome_message') }}
                </p>
                <div class="d-flex gap-4 mt-4">
                    <div class="d-flex align-items-center gap-2 auth-hero-feature">
                        <i class="bi bi-check-circle-fill text-gold"></i>
                        <span class="fs-md">{{ __('app.manage_tournaments') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 auth-hero-feature">
                        <i class="bi bi-check-circle-fill text-gold"></i>
                        <span class="fs-md">{{ __('app.track_results') }}</span>
                    </div>
                </div>
            </div>

            {{-- Right side: login form --}}
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card">
                    {{-- Mobile logo --}}
                    <div class="text-center d-lg-none mb-4">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <span class="text-gold fw-bold fs-xl">
                                <img src="{{ asset('img/icons/icon.png') }}" alt="Bracketa" width="35" height="35"> {{ config('app.name') }}
                            </span>
                        </a>
                    </div>

                    <div class="auth-logo">
                        <i class="bi bi-box-arrow-in-right text-dark"></i>
                    </div>
                    <h2>{{ __('app.login') }}</h2>
                    <p class="auth-subtitle">{{ __('app.login_subtitle') }}</p>

                    <x-form-errors class="alert-dark-danger mb-4" />

                    <form wire:submit="login">
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.email_or_username') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-person position-absolute input-icon-pos text-faded-30"></i>
                                <input type="text" class="form-control input-icon-right" placeholder="example@email.com" wire:model="identifier" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.password') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-lock position-absolute input-icon-pos text-faded-30"></i>
                                <input type="password" class="form-control input-icon-right" placeholder="••••••••" wire:model="password" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" wire:model="remember" id="remember">
                                <label class="form-check-label fs-md" for="remember">{{ __('app.remember_me') }}</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="fs-base text-gold-muted text-decoration-none">
                                {{ __('app.forgot_password') }}
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold auth-submit-btn" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="login">
                                <i class="bi bi-box-arrow-in-right me-2"></i> {{ __('app.login_button') }}
                            </span>
                            <span wire:loading wire:target="login">
                                <span class="spinner-border spinner-border-sm me-2"></span> {{ __('app.logging_in') }}
                            </span>
                        </button>
                    </form>

                    <div class="auth-divider">{{ __('app.or') }}</div>

                    <div class="text-center">
                        <span class="auth-footer-text">{{ __('app.no_account') }}</span>
                        <a href="{{ route('register') }}" class="auth-link">{{ __('app.create_free_account') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
