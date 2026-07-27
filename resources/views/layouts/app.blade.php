<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name') }} - {{ __('app.platform_desc') }}">
    <title>{{ config('app.name', 'Tournatak') }} - {{ $title ?? __('app.home') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @statusKitAssets(['bi'])
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="bg-body">

    {{-- Preloader --}}
    <div id="preloader" class="preloader">
        <div class="preloader-inner">
            <div class="preloader-ring"></div>
            <div class="preloader-logo"><i class="bi bi-trophy-fill"></i></div>
        </div>
    </div>

    {{-- Top Progress Bar --}}
    <div id="progress-bar" class="progress-bar-top"></div>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-main sticky-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand text-gold" href="{{ route('home') }}">
                <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
            </a>
            <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="{{ __('app.toggle_navigation') }}">
                <i class="bi bi-list text-white fs-4"></i>
            </button>

            {{-- Desktop nav --}}
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
                <ul class="navbar-nav {{ isRtl() ? 'me-auto' : 'ms-auto' }} mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i> {{ __('app.home') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('competitions.*') ? 'active' : '' }}" href="{{ route('competitions.index') }}">
                            <i class="bi bi-trophy"></i> {{ __('app.competitions') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teams.*') ? 'active' : '' }}" href="{{ route('teams.index') }}">
                            <i class="bi bi-shield-check"></i> {{ __('app.teams') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('players.*') ? 'active' : '' }}" href="{{ route('players.index') }}">
                            <i class="bi bi-people"></i> {{ __('app.players') }}
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav {{ isRtl() ? 'ms-auto' : 'me-auto' }} mb-2 mb-lg-0 align-items-lg-center">
                    @include('components.language-switcher')
                    <livewire:user.notification-bell />
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-grid-1x2"></i> {{ __('app.dashboard') }}
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
                                    <i class="bi bi-person-badge"></i> {{ __('app.my_account') }}
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown ms-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 rounded-pill" href="#" data-bs-toggle="dropdown" style="background: rgba(255,193,7,0.1); border: 1px solid rgba(255,193,7,0.2);">
                                <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:0.85rem;font-weight:800;">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="d-none d-lg-inline text-white fw-bold" style="font-size:0.9rem;">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="border-radius:12px;min-width:200px;">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('user.profile') }}">
                                        <i class="bi bi-person"></i> {{ __('app.profile') }}
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole('admin'))
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-grid-1x2"></i> {{ __('app.admin_panel') }}
                                    </a>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item d-flex align-items-center gap-2 text-danger" type="submit">
                                            <i class="bi bi-box-arrow-left"></i> {{ __('app.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-2">
                            <a class="nav-link px-3 py-2" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> {{ __('app.login') }}
                            </a>
                        </li>
                        <li class="nav-item ms-1">
                            <a class="btn btn-warning btn-sm px-3 py-2 fw-bold" href="{{ route('register') }}" style="border-radius:50px;">
                                <i class="bi bi-rocket-takeoff"></i> {{ __('app.get_started') }}
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            {{-- Mobile offcanvas nav --}}
            <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel" style="background:#0a0e1a;">
                <div class="offcanvas-header border-bottom" style="border-color:rgba(255,255,255,0.06) !important;">
                    <a class="navbar-brand text-gold" href="{{ route('home') }}">
                        <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="{{ __('app.close') }}"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="nav flex-column gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" style="color:rgba(255,255,255,0.7);font-weight:600;padding:12px 16px;border-radius:10px;">
                                <i class="bi bi-house-door me-2"></i> {{ __('app.home') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('competitions.*') ? 'active' : '' }}" href="{{ route('competitions.index') }}" style="color:rgba(255,255,255,0.7);font-weight:600;padding:12px 16px;border-radius:10px;">
                                <i class="bi bi-trophy me-2"></i> {{ __('app.competitions') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teams.*') ? 'active' : '' }}" href="{{ route('teams.index') }}" style="color:rgba(255,255,255,0.7);font-weight:600;padding:12px 16px;border-radius:10px;">
                                <i class="bi bi-shield-check me-2"></i> {{ __('app.teams') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('players.*') ? 'active' : '' }}" href="{{ route('players.index') }}" style="color:rgba(255,255,255,0.7);font-weight:600;padding:12px 16px;border-radius:10px;">
                                <i class="bi bi-people me-2"></i> {{ __('app.players') }}
                            </a>
                        </li>
                    </ul>
                    <hr style="border-color:rgba(255,255,255,0.06);">
                    @auth
                        <ul class="nav flex-column gap-1">
                            @if(auth()->user()->hasRole('admin'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}" style="color:rgba(255,255,255,0.7);font-weight:600;padding:12px 16px;border-radius:10px;">
                                        <i class="bi bi-grid-1x2 me-2"></i> {{ __('app.dashboard') }}
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('user.dashboard') }}" style="color:rgba(255,255,255,0.7);font-weight:600;padding:12px 16px;border-radius:10px;">
                                        <i class="bi bi-person-badge me-2"></i> {{ __('app.my_account') }}
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.profile') }}" style="color:rgba(255,255,255,0.7);font-weight:600;padding:12px 16px;border-radius:10px;">
                                    <i class="bi bi-person me-2"></i> {{ __('app.profile') }}
                                </a>
                            </li>
                            <li class="nav-item mt-2">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="nav-link w-100 text-start" type="submit" style="color:#ef4444;font-weight:600;padding:12px 16px;border-radius:10px;background:none;border:none;cursor:pointer;">
                                        <i class="bi bi-box-arrow-left me-2"></i> {{ __('app.logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    @else
                        <div class="d-flex flex-column gap-2 mt-2">
                            <a class="btn btn-outline-sport w-100" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-2"></i> {{ __('app.login') }}
                            </a>
                            <a class="btn btn-primary-sport w-100" href="{{ route('register') }}">
                                <i class="bi bi-rocket-takeoff me-2"></i> {{ __('app.get_started') }}
                            </a>
                        </div>
                    @endauth
                    <div class="mt-4">
                        <div class="fw-bold mb-2" style="color:rgba(255,255,255,0.5);font-size:0.85rem;"><i class="bi bi-globe2 me-2"></i>{{ __('app.platform_name') }}</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['ar' => '🇸🇦 العربية', 'en' => '🇬🇧 English', 'fr' => '🇫🇷 Français', 'es' => '🇪🇸 Español'] as $code => $label)
                                <a href="{{ route('lang.switch', $code) }}" class="btn btn-sm {{ app()->getLocale() === $code ? '' : '' }}" style="border-radius:8px;font-size:0.85rem;font-weight:600;{{ app()->getLocale() === $code ? 'background:rgba(255,193,7,0.15);color:#ffc107;border:1px solid rgba(255,193,7,0.3);' : 'background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.6);border:1px solid rgba(255,255,255,0.08);' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages (SweetAlert2 Toasts) --}}
    @if(session('success'))
        @push('scripts')
        <script>document.addEventListener('livewire:navigated', () => { Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ addslashes(session("success")) }}', showConfirmButton: false, timer: 4000, timerProgressBar: true, background: '#1a1f35', color: '#fff', borderColor: 'rgba(22,163,74,0.3)', iconColor: '#16a34a' }); });</script>
        @endpush
    @endif
    @if(session('error'))
        @push('scripts')
        <script>document.addEventListener('livewire:navigated', () => { Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: '{{ addslashes(session("error")) }}', showConfirmButton: false, timer: 5000, timerProgressBar: true, background: '#1a1f35', color: '#fff', borderColor: 'rgba(239,68,68,0.3)', iconColor: '#ef4444' }); });</script>
        @endpush
    @endif
    @if(session('info'))
        @push('scripts')
        <script>document.addEventListener('livewire:navigated', () => { Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: '{{ addslashes(session("info")) }}', showConfirmButton: false, timer: 4000, timerProgressBar: true, background: '#1a1f35', color: '#fff', borderColor: 'rgba(59,130,246,0.3)', iconColor: '#3b82f6' }); });</script>
        @endpush
    @endif

    {{-- Main Content --}}
    <main class="animate-page">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="footer-sports pt-5 pb-3">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="footer-brand text-gold mb-3">
                        <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                    </div>
                    <p style="color: rgba(255,255,255,0.5); max-width: 300px;">
                        {{ __('app.platform_desc') }}
                    </p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="social-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="social-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-white fw-bold mb-3">{{ __('app.platform_name') }}</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
                        <li><a href="{{ route('competitions.index') }}">{{ __('app.competitions') }}</a></li>
                        <li><a href="{{ route('teams.index') }}">{{ __('app.teams') }}</a></li>
                        <li><a href="{{ route('players.index') }}">{{ __('app.players') }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-white fw-bold mb-3">{{ __('app.account') }}</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        @auth
                            <li><a href="{{ route('user.dashboard') }}">{{ __('app.dashboard') }}</a></li>
                            <li><a href="{{ route('user.profile') }}">{{ __('app.profile') }}</a></li>
                        @else
                            <li><a href="{{ route('login') }}">{{ __('app.login') }}</a></li>
                            <li><a href="{{ route('register') }}">{{ __('app.register') }}</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white fw-bold mb-3">{{ __('app.contact_us') }}</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2" style="color:rgba(255,255,255,0.5);">
                        <li><i class="bi bi-envelope-fill text-gold me-2"></i> info@tournatak.com</li>
                        <li><i class="bi bi-telephone-fill text-gold me-2"></i> +213 XX XX XX XX</li>
                        <li><i class="bi bi-geo-alt-fill text-gold me-2"></i> {{ __('app.algeria') }}</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom pt-3 text-center">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('app.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
    <script>
        // Preloader
        window.addEventListener('load', function() {
            var preloader = document.getElementById('preloader');
            if (preloader) preloader.classList.add('hidden');
        });
        // Fallback: hide preloader after 3s
        setTimeout(function() {
            var preloader = document.getElementById('preloader');
            if (preloader) preloader.classList.add('hidden');
        }, 3000);

        // Livewire progress bar
        var progressBar = document.getElementById('progress-bar');
        document.addEventListener('livewire:navigate', function() {
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.classList.add('active');
                progressBar.style.width = '70%';
            }
        });
        document.addEventListener('livewire:navigated', function() {
            if (progressBar) {
                progressBar.style.width = '100%';
                setTimeout(function() {
                    progressBar.classList.remove('active');
                    progressBar.style.width = '0%';
                }, 300);
            }
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            if (nav) {
                nav.classList.toggle('scrolled', window.scrollY > 50);
            }
        });

        // Animate elements on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
    </script>
</body>
</html>
