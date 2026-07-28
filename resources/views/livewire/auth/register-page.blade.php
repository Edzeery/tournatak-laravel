<div class="auth-wrapper">
    {{-- Background Shapes --}}
    <div class="hero-shape hero-shape-auth-1 rtl-start"></div>
    <div class="hero-shape hero-shape-auth-2 rtl-right"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center">
            {{-- Left side: branding --}}
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center px-5 pos-rel-z2">
                <a href="{{ route('home') }}" class="text-decoration-none mb-4">
                    <span class="text-gold fw-bold fs-18">
                        <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                    </span>
                </a>
                <h1 class="text-white fw-bold mb-3 auth-hero-title">
                    {{ __('app.join') }} 
                    <span class="text-gold">{{ __('app.join_community') }}</span>
                </h1>
                <p class="auth-hero-desc">
                    {{ __('app.register_desc') }}
                </p>
                <div class="d-flex flex-column gap-3 mt-4">
                    <div class="d-flex align-items-center gap-3 auth-hero-feature">
                        <div class="bg-gold bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center auth-hero-feature-icon">
                            <i class="bi bi-trophy text-gold"></i>
                        </div>
                        <span class="fs-095">{{ __('app.feature_manage') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 auth-hero-feature">
                        <div class="bg-gold bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center auth-hero-feature-icon">
                            <i class="bi bi-shield-check text-gold"></i>
                        </div>
                        <span class="fs-095">{{ __('app.feature_register') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 auth-hero-feature">
                        <div class="bg-gold bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center auth-hero-feature-icon">
                            <i class="bi bi-graph-up-arrow text-gold"></i>
                        </div>
                        <span class="fs-095">{{ __('app.feature_track') }}</span>
                    </div>
                </div>
            </div>

            {{-- Right side: register form --}}
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card auth-card-narrow">
                    {{-- Mobile logo --}}
                    <div class="text-center d-lg-none mb-4">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <span class="text-gold fw-bold fs-xl">
                                <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                            </span>
                        </a>
                    </div>

                    <div class="auth-logo">
                        <i class="bi bi-person-plus text-dark"></i>
                    </div>
                    <h2>{{ __('app.create_new_account') }}</h2>
                    <p class="auth-subtitle">{{ __('app.register_subtitle') }}</p>

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

                    <form wire:submit="register">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">{{ __('app.username') }}</label>
                                <div class="position-relative">
                                    <i class="bi bi-at position-absolute input-icon-pos text-faded-30"></i>
                                    <input type="text" class="form-control input-icon-right-sm" placeholder="username" wire:model="username" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('app.full_name') }}</label>
                                <div class="position-relative">
                                    <i class="bi bi-person position-absolute input-icon-pos text-faded-30"></i>
                                    <input type="text" class="form-control input-icon-right-sm" placeholder="محمد أحمد" wire:model="name" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">{{ __('app.email') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-envelope position-absolute input-icon-pos text-faded-30"></i>
                                <input type="email" class="form-control input-icon-right" placeholder="example@email.com" wire:model="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.account_type') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-person-badge position-absolute input-icon-pos text-faded-30" style="z-index:5;"></i>
                                <select class="form-select input-icon-right" wire:model="role" required>
                                    <option value="user">{{ __('app.role_user') }}</option>
                                    <option value="competitor">{{ __('app.role_competitor') }}</option>
                                    <option value="captain">{{ __('app.role_captain') }}</option>
                                    <option value="player">{{ __('app.role_player') }}</option>
                                    <option value="organizer">{{ __('app.role_organizer') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">{{ __('app.password') }}</label>
                                <div class="position-relative">
                                    <i class="bi bi-lock position-absolute input-icon-pos text-faded-30"></i>
                                    <input type="password" class="form-control input-icon-right-sm" placeholder="••••••••" wire:model="password" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('app.confirm_password') }}</label>
                                <div class="position-relative">
                                    <i class="bi bi-lock-fill position-absolute input-icon-pos text-faded-30"></i>
                                    <input type="password" class="form-control input-icon-right-sm" placeholder="••••••••" wire:model="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold mt-4 auth-submit-btn" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="register">
                                <i class="bi bi-rocket-takeoff me-2"></i> {{ __('app.create_account') }}
                            </span>
                            <span wire:loading wire:target="register">
                                <span class="spinner-border spinner-border-sm me-2"></span> {{ __('app.creating') }}
                            </span>
                        </button>
                    </form>

                    <div class="auth-divider">{{ __('app.or') }}</div>

                    <div class="text-center">
                        <span class="auth-footer-text">{{ __('app.has_account') }}</span>
                        <a href="{{ route('login') }}" class="auth-link">{{ __('app.login_now') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
