<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - {{ $title ?? 'لوحة التحكم' }}</title>
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
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body>

    {{-- Sidebar --}}
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <div class="bg-gold text-dark rounded-3 d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:1.1rem;font-weight:800;">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <div class="text-white fw-bold" style="font-size:1rem;">{{ config('app.name') }}</div>
                    <small style="color:rgba(255,255,255,0.3);font-size:0.7rem;">{{ $title ?? 'لوحة التحكم' }}</small>
                </div>
            </a>
            <button class="btn btn-sm d-lg-none" onclick="toggleSidebar()" style="color:rgba(255,255,255,0.5);background:none;border:none;font-size:1.2rem;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">القائمة الرئيسية</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid-1x2-fill"></i> الرئيسية
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">إدارة</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people-fill"></i> المستخدمون
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}" href="{{ route('admin.teams.index') }}">
                        <i class="bi bi-shield-fill"></i> الفرق
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.players.*') ? 'active' : '' }}" href="{{ route('admin.players.index') }}">
                        <i class="bi bi-person-badge-fill"></i> اللاعبون
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.matches.*') ? 'active' : '' }}" href="{{ route('admin.matches.index') }}">
                        <i class="bi bi-calendar-event-fill"></i> المباريات
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">البطولات</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.competitions.*') ? 'active' : '' }}" href="{{ route('admin.competitions.index') }}">
                        <i class="bi bi-trophy-fill"></i> البطولات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.types.*') ? 'active' : '' }}" href="{{ route('admin.types.index') }}">
                        <i class="bi bi-tags-fill"></i> الأنواع
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.subtypes.*') ? 'active' : '' }}" href="{{ route('admin.subtypes.index') }}">
                        <i class="bi bi-bookmark-fill"></i> التصنيفات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}" href="{{ route('admin.positions.index') }}">
                        <i class="bi bi-geo-alt-fill"></i> المراكز
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section mt-4 pt-3" style="border-top:1px solid rgba(255,255,255,0.06);">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="bi bi-box-arrow-left"></i> العودة للموقع
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="nav-link w-100 text-end text-danger" type="submit">
                            <i class="bi bi-box-arrow-left"></i> تسجيل الخروج
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </aside>

    {{-- Backdrop for mobile --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    {{-- Main Content --}}
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm d-lg-none" onclick="toggleSidebar()" style="background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.2);border-radius:8px;color:var(--primary);">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold" style="color:var(--dark);font-size:1.1rem;">{{ $title ?? 'لوحة التحكم' }}</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                @include('components.language-switcher')
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:0.85rem;">
                        {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold" style="font-size:0.85rem;">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <small style="color:#94a3b8;font-size:0.7rem;">{{ Auth::user()->role ?? 'admin' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>

    {{-- Mobile sidebar toggle --}}
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarBackdrop').classList.toggle('show');
        }
    </script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
