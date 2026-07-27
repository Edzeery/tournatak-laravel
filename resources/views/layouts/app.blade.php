<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name') }} - {{ app()->getLocale() === 'ar' ? 'منصة إدارة البطولات والمسابقات الرياضية' : 'Sports Tournaments & Competitions Management Platform' }}">
    <title>{{ config('app.name', 'Tournatak') }} - {{ $title ?? (app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    @statusKitAssets(['bi'])
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="bg-body">

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-main sticky-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand text-gold" href="{{ route('home') }}">
                <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list text-white fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav {{ app()->getLocale() === 'ar' ? 'me-auto' : 'ms-auto' }} mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i> {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('competitions.*') ? 'active' : '' }}" href="{{ route('competitions.index') }}">
                            <i class="bi bi-trophy"></i> {{ app()->getLocale() === 'ar' ? 'البطولات' : 'Competitions' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teams.*') ? 'active' : '' }}" href="{{ route('teams.index') }}">
                            <i class="bi bi-shield-check"></i> {{ app()->getLocale() === 'ar' ? 'الفرق' : 'Teams' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('players.*') ? 'active' : '' }}" href="{{ route('players.index') }}">
                            <i class="bi bi-people"></i> {{ app()->getLocale() === 'ar' ? 'اللاعبون' : 'Players' }}
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav {{ app()->getLocale() === 'ar' ? 'ms-auto' : 'me-auto' }} mb-2 mb-lg-0 align-items-lg-center">
                    @include('components.language-switcher')
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-grid-1x2"></i> {{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
                                    <i class="bi bi-person-badge"></i> {{ app()->getLocale() === 'ar' ? 'حسابي' : 'My Account' }}
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
                                        <i class="bi bi-person"></i> {{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole('admin'))
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-grid-1x2"></i> {{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Admin Panel' }}
                                    </a>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item d-flex align-items-center gap-2 text-danger" type="submit">
                                            <i class="bi bi-box-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Logout' }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-2">
                            <a class="nav-link px-3 py-2" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Login' }}
                            </a>
                        </li>
                        <li class="nav-item ms-1">
                            <a class="btn btn-warning btn-sm px-3 py-2 fw-bold" href="{{ route('register') }}" style="border-radius:50px;">
                                <i class="bi bi-rocket-takeoff"></i> {{ app()->getLocale() === 'ar' ? 'ابدأ الآن' : 'Get Started' }}
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="container mt-3 animate-fade-in-down">
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mt-3 animate-fade-in-down">
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
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
                        {{ app()->getLocale() === 'ar' ? 'منصة رياضية متكاملة لإدارة البطولات والمسابقات والفرق واللاعبين في الجزائر والعالم العربي.' : 'A comprehensive sports platform for managing tournaments, competitions, teams and players.' }}
                    </p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-white fw-bold mb-3">{{ app()->getLocale() === 'ar' ? 'المنصة' : 'Platform' }}</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="{{ route('home') }}">{{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}</a></li>
                        <li><a href="{{ route('competitions.index') }}">{{ app()->getLocale() === 'ar' ? 'البطولات' : 'Competitions' }}</a></li>
                        <li><a href="{{ route('teams.index') }}">{{ app()->getLocale() === 'ar' ? 'الفرق' : 'Teams' }}</a></li>
                        <li><a href="{{ route('players.index') }}">{{ app()->getLocale() === 'ar' ? 'اللاعبون' : 'Players' }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-white fw-bold mb-3">{{ app()->getLocale() === 'ar' ? 'حسابي' : 'Account' }}</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        @auth
                            <li><a href="{{ route('user.dashboard') }}">{{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</a></li>
                            <li><a href="{{ route('user.profile') }}">{{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}</a></li>
                        @else
                            <li><a href="{{ route('login') }}">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Login' }}</a></li>
                            <li><a href="{{ route('register') }}">{{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white fw-bold mb-3">{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us' }}</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2" style="color:rgba(255,255,255,0.5);">
                        <li><i class="bi bi-envelope-fill text-gold me-2"></i> info@tournatak.com</li>
                        <li><i class="bi bi-telephone-fill text-gold me-2"></i> +213 XX XX XX XX</li>
                        <li><i class="bi bi-geo-alt-fill text-gold me-2"></i> {{ app()->getLocale() === 'ar' ? 'الجزائر' : 'Algeria' }}</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom pt-3 text-center">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
    @livewireScripts
    @stack('scripts')
</body>
</html>
