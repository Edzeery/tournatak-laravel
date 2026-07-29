<div>
    {{-- Hero Section --}}
    <section class="hero-sports text-white">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-badge animate-in">
                        <i class="bi bi-lightning-charge-fill"></i> {{ __('app.hero_tagline') }}
                    </div>
                    <h1 class="hero-title mb-4 animate-in animate-delay-1">
                        {{ __('app.hero_title') }}<br>
                        <span class="text-gold">{{ __('app.hero_title2') }}</span>
                    </h1>
                    <p class="hero-subtitle mb-5 animate-in animate-delay-2">
                        {{ __('app.hero_desc') }}
                    </p>
                    <div class="d-flex gap-3 flex-wrap animate-in animate-delay-3">
                        @auth
                            <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('user.dashboard') }}"
                                class="btn btn-primary-sport btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i> {{ __('app.dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary-sport btn-lg">
                                <i class="bi bi-rocket-takeoff me-2"></i> {{ __('app.cta_register') }}
                            </a>
                        @endauth
                        <a href="{{ route('competitions.index') }}" class="btn btn-outline-sport btn-lg">
                            <i class="bi bi-trophy me-2"></i> {{ __('app.cta_browse') }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="position-relative">
                        <div class="hero-shape hero-shape-lg" style="top:-20px;right:20px;"></div>
                        <div class="hero-shape hero-shape-md" style="bottom:20px;left:0;"></div>
                        <div class="position-relative pos-rel-z2">
                            <div
                                class="d-inline-flex flex-column align-items-center gap-3 p-4 rounded-4 hero-stats-panel">
                                <div class="d-flex gap-4">
                                    <div class="text-center">
                                        <div class="text-gold fw-bold fs-4xl">{{ $stats['competitions'] ?? 0 }}</div>
                                        <small class="text-chrome-subtle">{{ __('app.stat_competition') }}</small>
                                    </div>
                                    <div class="hero-stat-vdiv"></div>
                                    <div class="text-center">
                                        <div class="text-gold fw-bold fs-4xl">{{ $stats['teams'] ?? 0 }}</div>
                                        <small class="text-chrome-subtle">{{ __('app.stat_team') }}</small>
                                    </div>
                                </div>
                                <div class="hero-stat-hdiv"></div>
                                <div class="text-center">
                                    <div class="text-gold fw-bold fs-4xl">{{ $stats['players'] ?? 0 }}</div>
                                    <small class="text-chrome-subtle">{{ __('app.stat_player') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-gradient-bottom-tall"></div>
    </section>

    {{-- Stats Section --}}
    <section class="stats-section py-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4 animate-in animate-delay-1">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning bg-opacity-10 text-gold">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div class="stat-number">{{ $stats['competitions'] ?? 0 }}</div>
                        <div class="stat-label">{{ __('app.active_competition_stat') }}</div>
                    </div>
                </div>
                <div class="col-md-4 animate-in animate-delay-2">
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-green">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="stat-number">{{ $stats['teams'] ?? 0 }}</div>
                        <div class="stat-label">{{ __('app.registered_team_stat') }}</div>
                    </div>
                </div>
                <div class="col-md-4 animate-in animate-delay-3">
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-blue">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-number">{{ $stats['players'] ?? 0 }}</div>
                        <div class="stat-label">{{ __('app.player_stat_label') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Active Competitions --}}
    <section class="py-5 mt-4">
        <div class="container">
            <div class="section-header">
                <div class="section-badge"><i class="bi bi-fire"></i> {{ __('app.latest') }}</div>
                <h2>{{ __('app.active_competitions_section') }}</h2>
                <p>{{ __('app.active_competitions_desc') }}</p>
            </div>

            @if ($activeCompetitions->count())
                <div class="row g-4">
                    @foreach ($activeCompetitions as $competition)
                        <div class="col-md-6 col-lg-4">
                            <div class="competition-card h-100">
                                <div class="card-header-custom"></div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">{{ $competition->name }}</h5>
                                        <x-status-badge domain="competition" status="{{ $competition->status }}"
                                            set="bi" />
                                    </div>
                                    @if ($competition->description)
                                        <p class="text-muted mb-3 fs-md lh-relaxed">
                                            {{ Str::limit($competition->description, 120) }}
                                        </p>
                                    @endif
                                    <div class="card-meta">
                                        <span><i class="bi bi-calendar-event"></i>
                                            {{ formatDate($competition->start_date) }}</span>
                                        @if ($competition->organizer)
                                            <span><i class="bi bi-person"></i>
                                                {{ $competition->organizer->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-trophy d-block"></i>
                    <h4>{{ __('app.no_active_competitions') }}</h4>
                    <p>{{ __('app.competitions_coming_soon') }}</p>
                </div>
            @endif

            <div class="text-center mt-5">
                <a href="{{ route('competitions.index') }}" class="btn btn-outline-sport">
                    {{ __('app.competitions') }} <i class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }} me-1"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Latest Teams --}}
    @if ($teams->count())
        <section class="py-5 gradient-hero">
            <div class="container">
                <div class="section-header">
                    <div class="section-badge"><i class="bi bi-shield-fill"></i> {{ __('app.featured_teams') }}</div>
                    <h2>{{ __('app.latest_teams_desc') }}</h2>
                    <p>{{ __('app.browse_teams_desc') }}</p>
                </div>

                <div class="row g-4 justify-content-center">
                    @foreach ($teams as $team)
                        <div class="col-6 col-md-4 col-lg-2">
                            <div class="team-card">
                                @if ($team->logo)
                                    <img src="{{ $team->logo_url }}" alt="{{ $team->name }}"
                                        class="rounded-circle mb-3 logo-ring" width="72" height="72">
                                @else
                                    <div class="team-avatar bg-gold bg-opacity-50 text-dark mx-auto">
                                        {{ mb_substr($team->name, 0, 1) }}
                                    </div>
                                @endif
                                <h6 class="mb-1 fw-bold">{{ $team->name }}</h6>
                                @if ($team->points)
                                    <small class="text-muted"><i class="bi bi-star-fill text-gold"></i>
                                        {{ $team->points }} {{ __('app.points') }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5">
                    <a href="{{ route('teams.index') }}" class="btn btn-outline-sport">
                        {{ __('app.teams') }} <i class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }} me-1"></i>
                    </a>
                </div>
            </div>
            <div class="hero-shape hero-shape-md" style="top:-100px;left:-100px;"></div>
            <div class="hero-shape" style="width:200px;height:200px;bottom:-80px;right:-80px;"></div>
        </section>
    @endif

    {{-- CTA Section --}}
    <section class="py-5 cta-section">
        <div class="container text-center position-relative pos-rel-z2">
            <h2 class="text-white fw-bold mb-3 cta-title">{{ __('app.are_you_ready') }}</h2>
            <p class="mb-4 cta-desc">
                {{ __('app.cta_join_desc') }}
            </p>
            @auth
                <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('user.dashboard') }}"
                    class="btn btn-primary-sport btn-lg">
                    <i class="bi bi-speedometer2 me-2"></i> {{ __('app.dashboard') }}
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary-sport btn-lg">
                    <i class="bi bi-rocket-takeoff me-2"></i> {{ __('app.sign_up_now') }}
                </a>
            @endauth
        </div>
        <div class="hero-shape hero-shape-md" style="top:-100px;left:-100px;"></div>
        <div class="hero-shape" style="width:200px;height:200px;bottom:-80px;right:-80px;"></div>
    </section>
</div>
