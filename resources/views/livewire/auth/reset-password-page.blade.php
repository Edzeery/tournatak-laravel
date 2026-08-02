<div class="auth-wrapper">
    <div class="hero-shape hero-shape-auth-1 rtl-end"></div>
    <div class="hero-shape hero-shape-auth-2 rtl-left"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card">
                    <div class="text-center d-lg-none mb-4">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <span class="text-gold fw-bold fs-xl">
                                <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                            </span>
                        </a>
                    </div>

                    <div class="auth-logo">
                        <i class="bi bi-shield-lock-fill text-dark"></i>
                    </div>
                    <h2>{{ __('app.reset_password_title') }}</h2>
                    <p class="auth-subtitle">{{ __('app.reset_password_subtitle') }}</p>

                    <x-form-errors class="alert-dark-danger mb-4" />

                    <form wire:submit="resetPassword">
                        <input type="hidden" wire:model="token">

                        <div class="mb-3">
                            <label class="form-label">{{ __('app.email') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-envelope position-absolute text-theme-muted input-icon-pos"></i>
                                <input type="email" class="form-control input-icon-right" placeholder="example@email.com" wire:model="email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('app.new_password') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-lock position-absolute text-theme-muted input-icon-pos"></i>
                                <input type="password" class="form-control input-icon-right" placeholder="••••••••" wire:model="password" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">{{ __('app.confirm_password') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-lock-fill position-absolute text-theme-muted input-icon-pos"></i>
                                <input type="password" class="form-control input-icon-right" placeholder="••••••••" wire:model="password_confirmation" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold fs-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="resetPassword">
                                <i class="bi bi-check-circle me-2"></i> {{ __('app.reset_password_button') }}
                            </span>
                            <span wire:loading wire:target="resetPassword">
                                <span class="spinner-border spinner-border-sm me-2"></span> {{ __('app.saving') }}
                            </span>
                        </button>
                    </form>

                    <div class="auth-divider">{{ __('app.or') }}</div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="auth-link fs-md">
                            <i class="bi bi-arrow-right me-1"></i> {{ __('app.back_to_login') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
