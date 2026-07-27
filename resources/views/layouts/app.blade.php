<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <meta name="description" content="{{ config('app.name') }} - {{ __('app.platform_desc') }}">
    <title>{{ config('app.name', 'Tournatak') }} - {{ $title ?? __('app.home') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @statusKitAssets(['bi'])
    @livewireStyles
    @stack('styles')
</head>

<body class="bg-body">

    <a href="#main-content"
        class="visually-hidden-focusable position-absolute top-0 start-0 m-2 px-3 py-2 rounded skip-to-content">{{ __('app.skip_to_content') }}</a>

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

            <div class="d-flex align-items-center gap-2 d-lg-none">
                <livewire:user.notification-bell />
                <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileNav" aria-controls="mobileNav"
                    aria-label="{{ __('app.toggle_navigation') }}">
                    <i class="bi bi-list text-white fs-4"></i>
                </button>
            </div>

            {{-- Desktop nav --}}
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
                <ul class="navbar-nav {{ isRtl() ? 'ms-auto' : 'me-auto' }} mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">
                            {{ __('app.home') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('competitions.*') ? 'active' : '' }}"
                            href="{{ route('competitions.index') }}">
                            {{ __('app.competitions') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teams.*') ? 'active' : '' }}"
                            href="{{ route('teams.index') }}">
                            {{ __('app.teams') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('players.*') ? 'active' : '' }}"
                            href="{{ route('players.index') }}">
                            {{ __('app.players') }}
                        </a>
                    </li>
                </ul>
                <ul
                    class="navbar-nav {{ isRtl() ? 'ms-auto' : 'me-auto' }} mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                    @include('components.language-switcher')
                    <li class="nav-item">
                        <button class="nav-link border-0 bg-transparent d-flex align-items-center"
                            onclick="toggleTheme()" aria-label="{{ __('app.toggle_theme') }}">
                            <i class="bi theme-icon"></i>
                        </button>
                    </li>
                    <livewire:user.notification-bell />
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 navbar-user-btn"
                                href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="navbar-user-avatar">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="d-none d-xl-inline fw-bold fs-md text-white">{{ Auth::user()->name }}</span>
                            </a>
                            <div class="dropdown-menu navbar-dropdown">
                                <div class="dropdown-header-nav">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="navbar-user-avatar navbar-user-avatar-sm">
                                            {{ mb_substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-theme-primary">{{ Auth::user()->name }}</div>
                                            <small class="text-chrome-muted fs-sm">{{ Auth::user()->email }}</small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="navbar-role-badge">{{ ucfirst(Auth::user()->role) }}</span>
                                    </div>
                                </div>
                                <div class="dropdown-menu-items">
                                    <a class="dropdown-item" href="{{ route('user.profile') }}">
                                        <i class="bi bi-person"></i> <span>{{ __('app.profile') }}</span>
                                    </a>
                                    <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                        <i class="bi bi-grid-1x2"></i> <span>{{ __('app.dashboard') }}</span>
                                    </a>
                                    <a class="dropdown-item" href="{{ route('user.preferences') }}">
                                        <i class="bi bi-gear"></i> <span>{{ __('app.page_title_preferences') }}</span>
                                    </a>
                                    @if (auth()->user()->hasRole('admin'))
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-shield-lock"></i> <span>{{ __('app.admin_panel') }}</span>
                                        </a>
                                    @endif
                                </div>
                                <div class="dropdown-menu-items">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item dropdown-item-danger" type="submit">
                                            <i class="bi bi-box-arrow-left"></i> <span>{{ __('app.logout') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2" href="{{ route('login') }}">
                                {{ __('app.login') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-sm btn-gold fw-bold rounded-pill px-3" href="{{ route('register') }}">
                                {{ __('app.get_started') }}
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            {{-- Mobile offcanvas nav --}}
            <div class="offcanvas offcanvas-end d-lg-none offcanvas-dark" tabindex="-1" id="mobileNav"
                aria-labelledby="mobileNavLabel">
                <div class="offcanvas-header border-chrome-bottom">
                    <a class="navbar-brand text-gold" href="{{ route('home') }}">
                        <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="{{ __('app.close') }}"></button>
                </div>
                <div class="offcanvas-body">
                    @auth
                        <div class="offcanvas-user-card">
                            <div class="offcanvas-user-avatar">
                                {{ mb_substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                <small class="text-chrome-muted fs-sm">{{ Auth::user()->email }}</small>
                            </div>
                        </div>
                    @endauth

                    <ul class="nav flex-column offcanvas-nav-section">
                        <li class="nav-item">
                            <a class="nav-link nav-link-mobile {{ request()->routeIs('home') ? 'active' : '' }}"
                                href="{{ route('home') }}">
                                <i class="bi bi-house-door"></i> {{ __('app.home') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-mobile {{ request()->routeIs('competitions.*') ? 'active' : '' }}"
                                href="{{ route('competitions.index') }}">
                                <i class="bi bi-trophy"></i> {{ __('app.competitions') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-mobile {{ request()->routeIs('teams.*') ? 'active' : '' }}"
                                href="{{ route('teams.index') }}">
                                <i class="bi bi-shield-check"></i> {{ __('app.teams') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-mobile {{ request()->routeIs('players.*') ? 'active' : '' }}"
                                href="{{ route('players.index') }}">
                                <i class="bi bi-people"></i> {{ __('app.players') }}
                            </a>
                        </li>
                    </ul>

                    @auth
                        <div class="offcanvas-divider"></div>

                        <ul class="nav flex-column offcanvas-nav-section">
                            @if (auth()->user()->hasRole('admin'))
                                <li class="nav-item">
                                    <a class="nav-link nav-link-mobile" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-grid-1x2"></i> {{ __('app.dashboard') }}
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link nav-link-mobile" href="{{ route('user.dashboard') }}">
                                        <i class="bi bi-person-badge"></i> {{ __('app.my_account') }}
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link nav-link-mobile" href="{{ route('user.profile') }}">
                                    <i class="bi bi-person"></i> {{ __('app.profile') }}
                                </a>
                            </li>
                        </ul>

                        <div class="offcanvas-divider"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="nav-link nav-link-mobile nav-link-logout-mobile w-100" type="submit">
                                <i class="bi bi-box-arrow-left"></i> {{ __('app.logout') }}
                            </button>
                        </form>
                    @else
                        <div class="offcanvas-divider"></div>
                        <div class="d-flex flex-column gap-2">
                            <a class="btn btn-outline-light w-100 rounded-md fw-bold" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-2"></i> {{ __('app.login') }}
                            </a>
                            <a class="btn btn-gold w-100 rounded-md fw-bold" href="{{ route('register') }}">
                                <i class="bi bi-rocket-takeoff me-2"></i> {{ __('app.get_started') }}
                            </a>
                        </div>
                    @endauth

                    <div class="offcanvas-divider"></div>

                    <div class="offcanvas-bottom-section">
                        <button class="offcanvas-action-btn w-100" onclick="toggleTheme()">
                            <i class="bi theme-icon"></i>
                            <span>{{ __('app.toggle_theme') }}</span>
                        </button>
                        <div class="offcanvas-lang-row">
                            @foreach (['ar' => 'العربية', 'en' => 'English', 'fr' => 'Français', 'es' => 'Español'] as $code => $label)
                                <a href="{{ route('lang.switch', $code) }}"
                                    class="offcanvas-lang-btn {{ app()->getLocale() === $code ? 'active' : '' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages (SweetAlert2 Toasts) --}}
    @if (session('success'))
        @push('scripts')
            <script>
                document.addEventListener('livewire:navigated', () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: '{{ addslashes(session('success')) }}',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        background: '#1a1f35',
                        color: '#fff',
                        borderColor: 'rgba(22,163,74,0.3)',
                        iconColor: '#16a34a'
                    });
                });
            </script>
        @endpush
    @endif
    @if (session('error'))
        @push('scripts')
            <script>
                document.addEventListener('livewire:navigated', () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: '{{ addslashes(session('error')) }}',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        background: '#1a1f35',
                        color: '#fff',
                        borderColor: 'rgba(239,68,68,0.3)',
                        iconColor: '#ef4444'
                    });
                });
            </script>
        @endpush
    @endif
    @if (session('info'))
        @push('scripts')
            <script>
                document.addEventListener('livewire:navigated', () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: '{{ addslashes(session('info')) }}',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        background: '#1a1f35',
                        color: '#fff',
                        borderColor: 'rgba(59,130,246,0.3)',
                        iconColor: '#3b82f6'
                    });
                });
            </script>
        @endpush
    @endif

    {{-- Main Content --}}
    <main id="main-content" class="animate-page">
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
                    <p class="footer-desc">
                        {{ __('app.platform_desc') }}
                    </p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="social-icon" aria-label="Facebook"><i
                                class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon" aria-label="Twitter"><i
                                class="bi bi-twitter-x"></i></a>
                        <a href="#" class="social-icon" aria-label="Instagram"><i
                                class="bi bi-instagram"></i></a>
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
                    <ul class="list-unstyled d-flex flex-column gap-2 text-chrome-muted">
                        <li><i class="bi bi-envelope-fill text-gold me-2"></i> info@tournatak.com</li>
                        <li><i class="bi bi-telephone-fill text-gold me-2"></i> +213 XX XX XX XX</li>
                        <li><i class="bi bi-geo-alt-fill text-gold me-2"></i> {{ __('app.algeria') }}</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom pt-3 text-center">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}.
                    {{ __('app.all_rights_reserved') }}</p>
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
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
    </script>
</body>

</html>
