<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <title>{{ config('app.name') }} - {{ $title ?? __('app.dashboard') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @statusKitAssets(['bi'])
    @livewireStyles

    @stack('styles')
</head>

<body>

    {{-- Preloader --}}
    <div id="preloader" class="preloader">
        <div class="preloader-inner">
            <div class="preloader-ring"></div>
            <div class="preloader-logo"><i class="bi bi-trophy-fill"></i></div>
        </div>
    </div>

    {{-- Top Progress Bar --}}
    <div id="progress-bar" class="progress-bar-top"></div>

    {{-- Sidebar --}}
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <div class="bg-gold text-dark rounded-3 d-flex align-items-center justify-content-center w-38 h-38 fs-lg fw-bold">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <div class="text-white fw-bold fs-base">{{ config('app.name') }}</div>
                    <small class="text-chrome-subtle fs-xs">{{ $title ?? __('app.dashboard') }}</small>
                </div>
            </a>
            <button class="sidebar-collapse-btn d-none d-lg-flex" onclick="toggleSidebarCollapse()" title="{{ __('app.toggle_sidebar') }}" aria-label="{{ __('app.toggle_sidebar') }}">
                <i class="bi bi-chevron-left" id="collapseIcon"></i>
            </button>
            <button class="btn btn-sm d-lg-none border-0 bg-transparent text-chrome-muted fs-xl" onclick="toggleSidebar()" aria-label="{{ __('app.close_sidebar') }}">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">{{ __('app.main_menu') }}</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}" data-tooltip="{{ __('app.dashboard') }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>{{ __('app.dashboard') }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">{{ __('app.management') }}</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}" data-tooltip="{{ __('app.users') }}">
                        <i class="bi bi-people-fill"></i> <span>{{ __('app.users') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}"
                        href="{{ route('admin.teams.index') }}" data-tooltip="{{ __('app.teams') }}">
                        <i class="bi bi-shield-fill"></i> <span>{{ __('app.teams') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.players.*') ? 'active' : '' }}"
                        href="{{ route('admin.players.index') }}" data-tooltip="{{ __('app.players') }}">
                        <i class="bi bi-person-badge-fill"></i> <span>{{ __('app.players') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.matches.*') ? 'active' : '' }}"
                        href="{{ route('admin.matches.index') }}" data-tooltip="{{ __('app.matches') }}">
                        <i class="bi bi-calendar-event-fill"></i>
                        <span>{{ __('app.matches') }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">{{ __('app.competitions') }}</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.competitions.*') ? 'active' : '' }}"
                        href="{{ route('admin.competitions.index') }}" data-tooltip="{{ __('app.competitions') }}">
                        <i class="bi bi-trophy-fill"></i>
                        <span>{{ __('app.competitions') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.types.*') ? 'active' : '' }}"
                        href="{{ route('admin.types.index') }}" data-tooltip="{{ __('app.types') }}">
                        <i class="bi bi-tags-fill"></i> <span>{{ __('app.types') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.subtypes.*') ? 'active' : '' }}"
                        href="{{ route('admin.subtypes.index') }}" data-tooltip="{{ __('app.subtypes') }}">
                        <i class="bi bi-bookmark-fill"></i>
                        <span>{{ __('app.subtypes') }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section sidebar-section-bottom">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}"
                        href="{{ route('admin.positions.index') }}" data-tooltip="{{ __('app.positions') }}">
                        <i class="bi bi-geo-alt-fill"></i> <span>{{ __('app.positions') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.trash*') ? 'active' : '' }}"
                        href="{{ route('admin.trash') }}" data-tooltip="{{ __('app.trash') }}">
                        <i class="bi bi-trash3"></i> <span>{{ __('app.trash') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.security-log*') ? 'active' : '' }}"
                        href="{{ route('admin.security-log') }}" data-tooltip="{{ __('app.security_log') }}">
                        <i class="bi bi-shield-check"></i> <span>{{ __('app.security_log') }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section mt-2 pt-3 border-top border-chrome">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="bi bi-box-arrow-left"></i>
                        {{ __('app.back_to_site') }}
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="nav-link w-100 text-end text-danger" type="submit">
                            <i class="bi bi-box-arrow-left"></i>
                            {{ __('app.logout') }}
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
                <button class="btn btn-sm d-lg-none btn-gold-outline" onclick="toggleSidebar()" aria-label="{{ __('app.toggle_sidebar') }}">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold text-theme-primary fs-lg">{{ $title ?? __('app.dashboard') }}
                </h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <ul class="nav align-items-center mb-0 p-0 list-unstyled">
                    @include('components.language-switcher')
                </ul>
                <button class="btn btn-sm d-flex align-items-center justify-content-center w-36 h-36 rounded-md" onclick="toggleTheme()" aria-label="{{ __('app.toggle_theme') }}">
                    <i class="bi theme-icon"></i>
                </button>
                <livewire:user.notification-bell />
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold w-36 h-36 fs-base">
                        {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold fs-base">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <small class="text-theme-muted fs-xs">{{ Auth::user()->role ?? 'admin' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 admin-content">
            @if (session('success'))
                @push('scripts')
                <script>document.addEventListener('livewire:navigated', () => { Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ addslashes(session("success")) }}', showConfirmButton: false, timer: 4000, timerProgressBar: true, background: '#1a1f35', color: '#fff', borderColor: 'rgba(22,163,74,0.3)', iconColor: '#16a34a' }); });</script>
                @endpush
            @endif
            @if (session('error'))
                @push('scripts')
                <script>document.addEventListener('livewire:navigated', () => { Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: '{{ addslashes(session("error")) }}', showConfirmButton: false, timer: 5000, timerProgressBar: true, background: '#1a1f35', color: '#fff', borderColor: 'rgba(239,68,68,0.3)', iconColor: '#ef4444' }); });</script>
                @endpush
            @endif

            {{ $slot }}
        </div>
    </div>

    {{-- Mobile sidebar toggle --}}
    <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="{{ __('app.toggle_sidebar') }}">
        <i class="bi bi-list"></i>
    </button>

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

        // Sidebar collapse
        var sidebarCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        var sidebar = document.getElementById('sidebar');
        var collapseIcon = document.getElementById('collapseIcon');
        var htmlEl = document.documentElement;

        function applySidebarState() {
            if (sidebarCollapsed && window.innerWidth >= 992) {
                sidebar.classList.add('collapsed');
                htmlEl.classList.add('sidebar-collapsed');
                if (collapseIcon) collapseIcon.className = 'bi bi-chevron-right';
            } else {
                sidebar.classList.remove('collapsed');
                htmlEl.classList.remove('sidebar-collapsed');
                if (collapseIcon) collapseIcon.className = 'bi bi-chevron-left';
            }
        }

        applySidebarState();

        function toggleSidebarCollapse() {
            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebar_collapsed', sidebarCollapsed);
            applySidebarState();
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarBackdrop').classList.toggle('show');
        }
    </script>
    @livewireScripts
    @stack('scripts')
</body>

</html>
