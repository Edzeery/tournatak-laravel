@php $isRtl = isRtl(); @endphp
<div class="container py-4 container-page-md">
    {{-- Welcome Hero --}}
    <div class="card border-0 mb-4 animate-fade-in dashboard-hero-card">
        <div class="dashboard-hero-blob"></div>
        <div class="card-body p-4 position-relative" class="pos-rel-z2">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold animate-scale-in avatar-lg">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-white fw-bold mb-0 fs-xl">
                                {{ __('app.welcome_back') }}, {{ $user->name }}!
                            </h4>
                            <p class="fs-md text-chrome-subtle mb-0">
                                {{ __('app.account_overview_desc') }}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('user.profile') }}" class="btn btn-primary-sport btn-sm">
                            <i class="bi bi-person-gear me-1"></i> {{ __('app.edit_profile') }}
                        </a>
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-sport btn-sm">
                            <i class="bi bi-shield me-1"></i> {{ __('app.browse_teams') }}
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle dashboard-trophy">
                        <i class="bi bi-trophy-fill text-gold fs-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4 stagger-children">
        <div class="col-6 col-md-3">
            <div class="stat-card card-hover">
                <div class="stat-icon bg-gold bg-opacity-10 text-gold"><i class="bi bi-shield-fill"></i></div>
                <div class="stat-number count-animate fs-2xl">{{ $stats['teams'] }}</div>
                <div class="stat-label">{{ __('app.teams') }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card card-hover">
                <div class="stat-icon stat-icon-blue"><i class="bi bi-trophy-fill"></i></div>
                <div class="stat-number count-animate fs-2xl">{{ $stats['competitions'] }}</div>
                <div class="stat-label">{{ __('app.competitions') }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card card-hover">
                <div class="stat-icon stat-icon-green"><i class="bi bi-circle-fill"></i></div>
                <div class="stat-number count-animate fs-2xl">{{ $stats['goals'] }}</div>
                <div class="stat-label">{{ __('app.goals') }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card card-hover">
                <div class="stat-icon stat-icon-purple"><i class="bi bi-calendar-event-fill"></i></div>
                <div class="stat-number count-animate fs-2xl">{{ $stats['matches'] }}</div>
                <div class="stat-label">{{ __('app.matches') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="card border-0 animate-fade-in-up animate-delay-1">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-theme-primary">
                        <i class="bi bi-person-badge text-gold"></i> {{ __('app.account_info') }}
                    </h6>
                    <div class="text-center mb-4">
                        <div class="bg-gold text-dark rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-2 animate-scale-in avatar-md fs-2xl">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <h5 class="fw-bold mb-0 text-theme-primary">{{ $user->name }}</h5>
                        <small class="text-theme-muted">{{ $user->username }}</small>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 info-row">
                            <small class="text-muted"><i class="bi bi-envelope me-1"></i> {{ __('app.email') }}</small>
                            <small class="fw-bold fs-xs">{{ $user->email }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 info-row">
                            <small class="text-muted"><i class="bi bi-shield me-1"></i> {{ __('app.role') }}</small>
                            <span class="badge bg-warning-subtle text-warning fw-bold fs-sm">{{ $user->role ?? 'user' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 info-row">
                            <small class="text-muted"><i class="bi bi-calendar me-1"></i> {{ __('app.joined') }}</small>
                            <small class="fw-bold fs-xs">{{ formatDate($user->created_at) }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 info-row">
                            <small class="text-muted"><i class="bi bi-check-circle me-1"></i> {{ __('app.status') }}</small>
                            @if($user->is_verified)
                                <span class="badge fs-sm badge-verified">{{ __('app.verified') }}</span>
                            @else
                                <span class="badge bg-secondary fs-sm">{{ __('app.unverified') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8">
            {{-- Recent Matches --}}
            <div class="card border-0 mb-4 animate-fade-in-up animate-delay-2">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-theme-primary">
                        <i class="bi bi-calendar-event text-gold"></i> {{ __('app.recent_matches') }}
                    </h6>
                    @if($recentMatches->count())
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="fs-xs">{{ __('app.header_team1') }}</th>
                                        <th class="text-center fs-xs">{{ __('app.score') }}</th>
                                        <th class="fs-xs">{{ __('app.header_team2') }}</th>
                                        <th class="fs-xs">{{ __('app.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMatches as $match)
                                        <tr
                                            @if(in_array($match->phase, ['first_half','half_time','second_half','et_break','et_first_half','et_half_time','et_second_half']))
                                            x-data="{
                                                phase: '{{ $match->phase }}',
                                                fhs: {{ $match->first_half_started_at ? strtotime($match->first_half_started_at) * 1000 : 'null' }},
                                                shs: {{ $match->second_half_started_at ? strtotime($match->second_half_started_at) * 1000 : 'null' }},
                                                et1s: {{ $match->et_first_half_started_at ? strtotime($match->et_first_half_started_at) * 1000 : 'null' }},
                                                et2s: {{ $match->et_second_half_started_at ? strtotime($match->et_second_half_started_at) * 1000 : 'null' }},
                                                period: '', display: '00:00', _id: null,
                                                init() { this.tick(); this._id = setInterval(() => this.tick(), 1000); },
                                                tick() {
                                                    const now = Date.now();
                                                    if (this.phase === 'full_time') { this.period = 'FT'; this.display = 'FT'; return; }
                                                    if (this.phase === 'first_half' && this.fhs) {
                                                        const s = Math.max(0, Math.floor((now - this.fhs) / 1000));
                                                        const m = Math.floor(s / 60); const sec = s % 60;
                                                        this.period = '1st'; this.display = String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                                                        if (m >= 45) this.display += '+' + (m - 45);
                                                        return;
                                                    }
                                                    if (this.phase === 'half_time') { this.period = 'HT'; this.display = 'HT'; return; }
                                                    if (this.phase === 'second_half' && this.shs) {
                                                        const s = Math.max(0, Math.floor((now - this.shs) / 1000));
                                                        const m = Math.floor(s / 60); const sec = s % 60;
                                                        const t = 45 + m;
                                                        this.period = '2nd'; this.display = String(t).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                                                        if (m >= 45) this.display += '+' + (m - 45);
                                                        return;
                                                    }
                                                    if (this.phase === 'et_first_half' && this.et1s) {
                                                        const s = Math.max(0, Math.floor((now - this.et1s) / 1000));
                                                        const m = Math.floor(s / 60); const sec = s % 60;
                                                        this.period = 'ET1'; this.display = String(90 + m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                                                        return;
                                                    }
                                                    if (this.phase === 'et_second_half' && this.et2s) {
                                                        const s = Math.max(0, Math.floor((now - this.et2s) / 1000));
                                                        const m = Math.floor(s / 60); const sec = s % 60;
                                                        this.period = 'ET2'; this.display = String(105 + m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                                                        return;
                                                    }
                                                    this.period = ''; this.display = '—';
                                                },
                                                destroy() { if (this._id) clearInterval(this._id); }
                                            }"
                                            @endif
                                        >
                                            <td class="fw-bold fs-base">{{ $match->team1->name ?? '—' }}</td>
                                            <td class="text-center">
                                                @if($match->status === 'in_progress')
                                                    <span class="badge bg-danger rounded-pill px-3 fs-base live-pulse">
                                                        {{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}
                                                    </span>
                                                    <div class="fs-xs text-danger mt-1">
                                                        <span x-text="period"></span> <span x-text="display"></span>
                                                    </div>
                                                @else
                                                    <span class="badge bg-gold bg-opacity-20 rounded-pill px-3 fs-base">
                                                        {{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="fw-bold fs-base">{{ $match->team2->name ?? '—' }}</td>
                                            <td class="text-theme-muted fs-xs">{{ formatDate($match->match_date, 'd/m') ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x d-block mb-2 fs-3xl text-slate"></i>
                            <p class="text-theme-muted fs-md">{{ __('app.no_matches_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card border-0 animate-fade-in-up animate-delay-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-theme-primary">
                        <i class="bi bi-lightning-fill text-gold"></i> {{ __('app.quick_actions') }}
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('user.profile') }}" class="btn btn-sm btn-outline-primary rounded-md">
                            <i class="bi bi-person-gear"></i> {{ __('app.profile') }}
                        </a>
                        <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-success rounded-md">
                            <i class="bi bi-shield"></i> {{ __('app.browse_teams') }}
                        </a>
                        <a href="{{ route('competitions.index') }}" class="btn btn-sm btn-outline-warning rounded-md">
                            <i class="bi bi-trophy"></i> {{ __('app.competitions') }}
                        </a>
                        <a href="{{ route('players.index') }}" class="btn btn-sm btn-outline-info rounded-md">
                            <i class="bi bi-people"></i> {{ __('app.players') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
