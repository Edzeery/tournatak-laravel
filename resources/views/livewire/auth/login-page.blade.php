<div class="auth-wrapper">
    {{-- Background Shapes --}}
    <div class="hero-shape" style="width:600px;height:600px;top:-300px;right:-200px;"></div>
    <div class="hero-shape" style="width:400px;height:400px;bottom:-200px;left:-150px;"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center">
            {{-- Left side: branding --}}
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center px-5" style="position:relative;z-index:2;">
                <a href="{{ route('home') }}" class="text-decoration-none mb-4">
                    <span class="text-gold fw-bold" style="font-size:1.8rem;">
                        <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                    </span>
                </a>
                <h1 class="text-white fw-bold mb-3" style="font-size:2.5rem; line-height:1.3;">
                    {{ __('app.welcome_back') }}<br>
                    <span class="text-gold">{{ __('app.follow_your_journey') }}</span>
                </h1>
                <p style="color:rgba(255,255,255,0.5); font-size:1.1rem; max-width:420px;">
                    {{ __('app.welcome_message') }}
                </p>
                <div class="d-flex gap-4 mt-4">
                    <div class="d-flex align-items-center gap-2" style="color:rgba(255,255,255,0.4);">
                        <i class="bi bi-check-circle-fill text-gold"></i>
                        <span style="font-size:0.9rem;">{{ __('app.manage_tournaments') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="color:rgba(255,255,255,0.4);">
                        <i class="bi bi-check-circle-fill text-gold"></i>
                        <span style="font-size:0.9rem;">{{ __('app.track_results') }}</span>
                    </div>
                </div>
            </div>

            {{-- Right side: login form --}}
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card">
                    {{-- Mobile logo --}}
                    <div class="text-center d-lg-none mb-4">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <span class="text-gold fw-bold" style="font-size:1.5rem;">
                                <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                            </span>
                        </a>
                    </div>

                    <div class="auth-logo">
                        <i class="bi bi-box-arrow-in-right text-dark"></i>
                    </div>
                    <h2>{{ __('app.login') }}</h2>
                    <p class="auth-subtitle">{{ __('app.login_subtitle') }}</p>

                    @if($errors->any())
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.2);color:#fca5a5;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <div style="font-size:0.85rem;">{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form wire:submit="login">
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.email_or_username') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-person position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="text" class="form-control" style="padding-inline-end:42px;" placeholder="example@email.com" wire:model="identifier" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.password') }}</label>
                            <div class="position-relative">
                                <i class="bi bi-lock position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="password" class="form-control" style="padding-inline-end:42px;" placeholder="••••••••" wire:model="password" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" wire:model="remember" id="remember">
                                <label class="form-check-label" for="remember" style="font-size:0.9rem;">{{ __('app.remember_me') }}</label>
                            </div>
                            <a href="{{ route('password.request') }}" style="font-size:0.85rem;color:rgba(255,193,7,0.8);text-decoration:none;">
                                {{ __('app.forgot_password') }}
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold" style="font-size:1.05rem;" wire:loading.attr="disabled">
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
                        <span style="color:rgba(255,255,255,0.5);">{{ __('app.no_account') }}</span>
                        <a href="{{ route('register') }}" class="auth-link">{{ __('app.create_free_account') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
