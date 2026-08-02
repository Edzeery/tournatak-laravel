<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <title>{{ config('app.name') }} - {{ $title ?? __('app.dashboard') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#0a0e1a">
    <meta name="msapplication-TileColor" content="#0a0e1a">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @statusKitAssets(['bi'])
    @livewireStyles

    @stack('styles')
</head>

<body>

    <a href="#main-content"
        class="visually-hidden-focusable position-absolute top-0 start-0 m-2 px-3 py-2 rounded skip-to-content">{{ __('app.skip_to_content') }}</a>

    {{-- Preloader --}}
    <div id="preloader" class="preloader">
        <div class="preloader-inner">
            <div class="preloader-ring"></div>
            <div class="preloader-logo"><img src="{{ asset('favicon.ico') }}" alt="{{ config('app.name') }}" width="30" height="30"></div>
        </div>
    </div>

    {{-- Top Progress Bar --}}
    <div id="progress-bar" class="progress-bar-top"></div>

    {{-- Sidebar --}}
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2 sidebar-logo">
                <div class="sidebar-logo-icon">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <div class="text-white fw-bold fs-base">{{ config('app.name') }}</div>
                    <small class="text-chrome-subtle fs-xs">{{ $title ?? __('app.dashboard') }}</small>
                </div>
            </a>
            <button class="btn btn-sm d-lg-none border-0 bg-transparent text-chrome-muted fs-xl"
                onclick="toggleSidebar()" aria-label="{{ __('app.close_sidebar') }}">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="sidebar-user-section d-none d-lg-flex">
            <div class="sidebar-user-avatar">
                {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name text-truncate user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="sidebar-user-role text-truncate  user-name">{{ Auth::user()->role ?? 'admin' }}</div>
            </div>
        </div>

        <div class="sidebar-nav-scroll">
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
                    @can('manage users')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}" data-tooltip="{{ __('app.users') }}">
                            <i class="bi bi-people-fill"></i> <span>{{ __('app.users') }}</span>
                        </a>
                    </li>
                    @endcan

                    @can('manage teams')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }} {{ request()->routeIs('admin.teams.*') ? 'open' : '' }}"
                            href="#" onclick="toggleSubmenu(this, 'submenu-teams')"
                            data-tooltip="{{ __('app.teams') }}">
                            <i class="bi bi-shield-fill"></i> <span>{{ __('app.teams') }}</span>
                            <i class="bi bi-chevron-down nav-link-arrow"></i>
                        </a>
                        <div class="sidebar-submenu {{ request()->routeIs('admin.teams.*') ? 'show' : '' }}"
                            id="submenu-teams">
                            <a class="nav-link {{ request()->routeIs('admin.teams.index') ? 'active' : '' }}"
                                href="{{ route('admin.teams.index') }}">
                                <i class="bi bi-list-ul"></i> <span>{{ __('app.all_teams') }}</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.teams.create') ? 'active' : '' }}"
                                href="{{ route('admin.teams.create') }}">
                                <i class="bi bi-plus-circle"></i> <span>{{ __('app.add_team') }}</span>
                            </a>
                        </div>
                    </li>
                    @endcan

                    @can('manage players')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.players.*') ? 'active' : '' }} {{ request()->routeIs('admin.players.*') ? 'open' : '' }}"
                            href="#" onclick="toggleSubmenu(this, 'submenu-players')"
                            data-tooltip="{{ __('app.players') }}">
                            <i class="bi bi-person-badge-fill"></i> <span>{{ __('app.players') }}</span>
                            <i class="bi bi-chevron-down nav-link-arrow"></i>
                        </a>
                        <div class="sidebar-submenu {{ request()->routeIs('admin.players.*') ? 'show' : '' }}"
                            id="submenu-players">
                            <a class="nav-link {{ request()->routeIs('admin.players.index') ? 'active' : '' }}"
                                href="{{ route('admin.players.index') }}">
                                <i class="bi bi-list-ul"></i> <span>{{ __('app.all_players') }}</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.players.create') ? 'active' : '' }}"
                                href="{{ route('admin.players.create') }}">
                                <i class="bi bi-plus-circle"></i> <span>{{ __('app.add_player') }}</span>
                            </a>
                        </div>
                    </li>
                    @endcan

                    @can('manage matches')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.matches.*') ? 'active' : '' }} {{ request()->routeIs('admin.matches.*') ? 'open' : '' }}"
                            href="#" onclick="toggleSubmenu(this, 'submenu-matches')"
                            data-tooltip="{{ __('app.matches') }}">
                            <i class="bi bi-calendar-event-fill"></i>
                            <span>{{ __('app.matches') }}</span>
                            <i class="bi bi-chevron-down nav-link-arrow"></i>
                        </a>
                        <div class="sidebar-submenu {{ request()->routeIs('admin.matches.*') ? 'show' : '' }}"
                            id="submenu-matches">
                            <a class="nav-link {{ request()->routeIs('admin.matches.index') ? 'active' : '' }}"
                                href="{{ route('admin.matches.index') }}">
                                <i class="bi bi-list-ul"></i> <span>{{ __('app.all_matches') }}</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.matches.create') ? 'active' : '' }}"
                                href="{{ route('admin.matches.create') }}">
                                <i class="bi bi-plus-circle"></i> <span>{{ __('app.add_match') }}</span>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.referees.*') ? 'active' : '' }}"
                            href="{{ route('admin.referees.index') }}" data-tooltip="{{ __('app.referees') }}">
                            <i class="bi bi-person-check-fill"></i> <span>{{ __('app.referees') }}</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-label">{{ __('app.competitions_domains') }}</div>
                <ul class="nav flex-column">
                    @can('manage settings')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.domains.*') ? 'active' : '' }}"
                            href="{{ route('admin.domains.index') }}" data-tooltip="{{ __('app.domains') }}">
                            <i class="bi bi-grid-1x2-fill"></i> <span>{{ __('app.domains') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('manage competitions')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.competitions.*') ? 'active' : '' }} {{ request()->routeIs('admin.competitions.*') ? 'open' : '' }}"
                            href="#" onclick="toggleSubmenu(this, 'submenu-competitions')"
                            data-tooltip="{{ __('app.competitions') }}">
                            <i class="bi bi-trophy-fill"></i>
                            <span>{{ __('app.competitions') }}</span>
                            <i class="bi bi-chevron-down nav-link-arrow"></i>
                        </a>
                        <div class="sidebar-submenu {{ request()->routeIs('admin.competitions.*') ? 'show' : '' }}"
                            id="submenu-competitions">
                            <a class="nav-link {{ request()->routeIs('admin.competitions.index') ? 'active' : '' }}"
                                href="{{ route('admin.competitions.index') }}">
                                <i class="bi bi-list-ul"></i> <span>{{ __('app.all_competitions') }}</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.competitions.create') ? 'active' : '' }}"
                                href="{{ route('admin.competitions.create') }}">
                                <i class="bi bi-plus-circle"></i> <span>{{ __('app.add_competition') }}</span>
                            </a>
                        </div>
                    </li>
                    @endcan
                    @can('manage competitions')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.registrations.*') ? 'active open' : '' }}"
                            href="#" onclick="toggleSubmenu(this, 'submenu-registrations')"
                            data-tooltip="{{ __('app.registrations') }}">
                            <i class="bi bi-person-plus-fill"></i> <span>{{ __('app.registrations') }}</span>
                            <i class="bi bi-chevron-down nav-link-arrow"></i>
                        </a>
                        <div class="sidebar-submenu {{ request()->routeIs('admin.registrations.*') ? 'show' : '' }}"
                            id="submenu-registrations">
                            <a class="nav-link {{ request()->routeIs('admin.registrations.index') ? 'active' : '' }}"
                                href="{{ route('admin.registrations.index') }}">
                                <i class="bi bi-list-ul"></i> <span>{{ __('app.all_registrations') }}</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.registrations.create.individual') ? 'active' : '' }}"
                                href="{{ route('admin.registrations.create.individual') }}">
                                <i class="bi bi-person-plus"></i> <span>{{ __('app.add_individual_registration_short') }}</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.registrations.create.team') ? 'active' : '' }}"
                                href="{{ route('admin.registrations.create.team') }}">
                                <i class="bi bi-shield-plus"></i> <span>{{ __('app.add_team_registration_short') }}</span>
                            </a>
                        </div>
                    </li>
                    @endcan
                    @can('manage casual competitions')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.competitions.create-casual') ? 'active' : '' }}"
                            href="{{ route('admin.competitions.create-casual') }}">
                            <i class="bi bi-plus-circle-dotted"></i> <span>{{ __('app.create_casual_competition') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('manage competition types')
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
                    @endcan
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-label">{{ __('app.system') }}</div>
                <ul class="nav flex-column">
                    @can('manage settings')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.sports.*') ? 'active' : '' }}"
                            href="{{ route('admin.sports.index') }}" data-tooltip="{{ __('app.sports') }}">
                            <i class="bi bi-trophy-fill"></i> <span>{{ __('app.sports') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}"
                            href="{{ route('admin.positions.index') }}" data-tooltip="{{ __('app.positions') }}">
                            <i class="bi bi-geo-alt-fill"></i> <span>{{ __('app.positions') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('manage admin users')
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
                    @endcan
                </ul>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="sidebar-footer-user d-lg-none">
                <div class="sidebar-user-avatar sidebar-user-avatar-sm">
                    {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div class="sidebar-user-role">{{ Auth::user()->role ?? 'admin' }}</div>
                </div>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}" data-tooltip="{{ __('app.back_to_site') }}">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>{{ __('app.back_to_site') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="nav-link nav-link-logout w-100" type="submit"
                            data-tooltip="{{ __('app.logout') }}">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>{{ __('app.logout') }}</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </aside>

    {{-- Sidebar Collapse Toggle (outside sidebar) --}}
    <button class="sidebar-collapse-toggle" id="collapseToggle" onclick="toggleSidebarCollapse()"
        title="{{ __('app.toggle_sidebar') }}" aria-label="{{ __('app.toggle_sidebar') }}">
        <i class="bi bi-chevron-{{ isRtl() ? 'left' : 'right' }}" id="collapseIcon"></i>
    </button>

    {{-- Backdrop for mobile --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    {{-- Main Content --}}
    <div class="admin-main" id="main-content">
        <div class="admin-topbar-bg">
            <div class="admin-topbar mx-auto">

                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm d-lg-none btn-gold-outline" onclick="toggleSidebar()"
                        aria-label="{{ __('app.toggle_sidebar') }}">
                        <i class="bi bi-list"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-theme-primary fs-lg">{{ $title ?? __('app.dashboard') }}
                    </h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <ul class="nav align-items-center mb-0 p-0 list-unstyled">
                        @include('components.language-switcher')
                    </ul>
                    <button class="btn btn-sm d-flex align-items-center justify-content-center w-36 h-36 rounded-md"
                        onclick="toggleTheme()" aria-label="{{ __('app.toggle_theme') }}">
                        <i class="bi theme-icon"></i>
                    </button>
                    <livewire:user.notification-bell />
                    <a href="{{ route('user.profile') }}" class="d-flex align-items-center gap-2 text-decoration-none topbar-user">
                        <div class="topbar-user-avatar">
                            {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-bold fs-base text-theme-primary">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <small class="text-theme-muted fs-xs">{{ ucfirst(Auth::user()->role ?? 'admin') }}</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-4 admin-content mx-auto container">
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
            var isRtl = document.documentElement.dir === 'rtl';
            if (sidebarCollapsed && window.innerWidth >= 992) {
                sidebar.classList.add('collapsed');
                htmlEl.classList.add('sidebar-collapsed');
                if (collapseIcon) collapseIcon.className = 'bi bi-chevron-' + (isRtl ? 'left' : 'right');
            } else {
                sidebar.classList.remove('collapsed');
                htmlEl.classList.remove('sidebar-collapsed');
                if (collapseIcon) collapseIcon.className = 'bi bi-chevron-' + (isRtl ? 'right' : 'left');
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

        function toggleSubmenu(link, submenuId) {
            var submenu = document.getElementById(submenuId);
            if (!submenu) return;

            var isOpen = submenu.classList.contains('show');

            // Close all other submenus
            document.querySelectorAll('.sidebar-submenu.show').forEach(function(el) {
                if (el.id !== submenuId) {
                    el.classList.remove('show');
                    el.previousElementSibling.classList.remove('open');
                }
            });

            // Toggle current
            if (isOpen) {
                submenu.classList.remove('show');
                link.classList.remove('open');
            } else {
                submenu.classList.add('show');
                link.classList.add('open');
            }
        }
    </script>
    @livewireScripts
    @stack('scripts')
</body>

</html>
