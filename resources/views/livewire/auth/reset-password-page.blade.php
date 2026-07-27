<div class="auth-wrapper">
    <div class="hero-shape" style="width:600px;height:600px;top:-300px;right:-200px;"></div>
    <div class="hero-shape" style="width:400px;height:400px;bottom:-200px;left:-150px;"></div>

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

                    <form wire:submit="resetPassword">
                        <input type="hidden" wire:model="token">

                        <div class="mb-3">
                            <label class="form-label">{{ __('app.email') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-envelope position-absolute text-theme-muted" style="right:14px;top:50%;transform:translateY(-50%);"></i>
                                <input type="email" class="form-control" style="padding-inline-end:42px;" placeholder="example@email.com" wire:model="email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('app.new_password') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-lock position-absolute text-theme-muted" style="right:14px;top:50%;transform:translateY(-50%);"></i>
                                <input type="password" class="form-control" style="padding-inline-end:42px;" placeholder="••••••••" wire:model="password" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">{{ __('app.confirm_password') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-lock-fill position-absolute text-theme-muted" style="right:14px;top:50%;transform:translateY(-50%);"></i>
                                <input type="password" class="form-control" style="padding-inline-end:42px;" placeholder="••••••••" wire:model="password_confirmation" required>
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
