<div wire:poll.10s>
    {{-- Hero --}}
    <section class="hero-sports hero-sports-sm text-white position-relative overflow-hidden">
        <div class="hero-gradient-bottom"></div>
        <div class="container hero-content">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="hero-badge d-inline-flex mb-3">
                        <i class="bi bi-trophy-fill me-1"></i>
                        {{ $competition->type?->name ?? __('app.competition') }}
                    </div>
                    <h1 class="fw-bold mb-2 fs-4xl">{{ $competition->name }}</h1>
                    <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
                        <x-status-badge domain="competition" status="{{ $competition->status }}" set="bi" />
                        @if($competition->format)
                            <span class="badge-sport">{{ __("app.format_{$competition->format}") ?? $competition->format }}</span>
                        @endif
                        <span class="text-chrome-muted fs-sm">
                            <i class="bi bi-calendar-event text-gold me-1"></i>
                            {{ $competition->start_date?->format('d/m/Y') ?? '—' }} — {{ $competition->end_date?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                    @if($competition->description)
                        <p class="mt-3 text-chrome-muted mb-0">{{ Str::limit($competition->description, 200) }}</p>
                    @endif
                </div>
                <div class="col-lg-5">
                    <div class="hero-stats-panel">
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="hero-stat-item">
                                    <div class="stat-number">{{ $competition->teams->count() }}</div>
                                    <div class="stat-label">{{ __('app.teams') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="hero-stat-item">
                                    <div class="stat-number">{{ $competition->matches->count() }}</div>
                                    <div class="stat-label">{{ __('app.matches') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="hero-stat-item">
                                    <div class="stat-number">{{ $competition->matches->where('status', 'completed')->count() }}</div>
                                    <div class="stat-label">{{ __('app.completed') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <div class="container py-5 mt-neg-20">
        {{-- Tabs --}}
        <ul class="nav nav-tabs-custom mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#matches-tab" type="button" role="tab">
                    <i class="bi bi-calendar-event me-1"></i> {{ __('app.matches') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#standings-tab" type="button" role="tab">
                    <i class="bi bi-bar-chart me-1"></i> {{ __('app.standings') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#teams-tab" type="button" role="tab">
                    <i class="bi bi-people me-1"></i> {{ __('app.teams') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#scorers-tab" type="button" role="tab">
                    <i class="bi bi-crosshair me-1"></i> {{ __('app.top_scorers') }}
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Matches Tab --}}
            <div class="tab-pane fade show active" id="matches-tab" role="tabpanel">
                @php
                    $todayStr = today()->format('Y-m-d');
                    $yesterdayStr = today()->subDay()->format('Y-m-d');
                    $tomorrowStr = today()->addDay()->format('Y-m-d');
                @endphp

                {{-- Date Bar --}}
                <div class="date-bar">
                    <button class="date-bar-btn" wire:click="prevDay" title="{{ __('app.prev_day') }}">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    <div class="date-bar-track">
                        @foreach($dateRange as $d)
                            @php
                                $isYesterday = $d['date'] === $yesterdayStr;
                                $isToday = $d['isToday'];
                                $isTomorrow = $d['date'] === $tomorrowStr;
                            @endphp
                            <button class="date-cell {{ $d['isSelected'] ? 'selected' : '' }} {{ $d['hasMatches'] ? 'has-matches' : '' }}" wire:click="goToDate('{{ $d['date'] }}')">
                                <span class="date-cell-dow">{{ $d['dow'] }}</span>
                                <span class="date-cell-day">{{ $d['day'] }}</span>
                                <span class="date-cell-month">{{ $d['month'] }}</span>
                                @if($isToday)
                                    <span class="date-cell-label">{{ __('app.today') }}</span>
                                @elseif($isYesterday)
                                    <span class="date-cell-label">{{ __('app.yesterday') }}</span>
                                @elseif($isTomorrow)
                                    <span class="date-cell-label">{{ __('app.tomorrow') }}</span>
                                @endif
                                @if($d['hasMatches'])
                                    <span class="date-cell-dot"></span>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <div class="date-bar-actions">
                        <button class="date-bar-btn" wire:click="nextDay" title="{{ __('app.next_day') }}">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="date-bar-picker">
                            <input type="text" class="flatpickr-input date-bar-btn" wire:change="goToDate($event.target.value)" value="{{ $this->selectedDate }}" data-alt-format="M j, Y" data-alt-input-class="flatpickr-alt-compact" readonly>
                        </div>
                    </div>
                </div>

                {{-- Search + Filter Buttons --}}
                <div class="matches-toolbar">
                    <div class="matches-search">
                        <i class="bi bi-search"></i>
                        <input type="text" class="matches-search-input" placeholder="{{ __('app.search_teams') }}" wire:model.live.debounce.300ms="search">
                        @if($search)
                            <button class="matches-search-clear" wire:click="$set('search', '')"><i class="bi bi-x"></i></button>
                        @endif
                    </div>
                    <div class="matches-filters">
                        <button class="filter-btn {{ $this->filterMode === 'date' ? 'active' : '' }}" wire:click="setFilter('date')">{{ __('app.filter_date') }}</button>
                        <button class="filter-btn {{ $this->filterMode === 'all' ? 'active' : '' }}" wire:click="setFilter('all')">{{ __('app.all') }}</button>
                        <button class="filter-btn {{ $this->filterMode === 'live' ? 'active' : '' }}" wire:click="setFilter('live')">{{ __('app.live') }}</button>
                    </div>
                </div>

                {{-- Matches List --}}
                @if($filteredMatches->count())
                    @foreach($filteredMatches as $match)
                        <div class="match-card" role="button" onclick="window.location='{{ route('matches.live', $match) }}'" wire:key="match-{{ $match->id }}">
                            <div class="match-card-inner">
                                <div class="match-team match-team-home">
                                    <span class="match-team-name">{{ $match->team1?->name ?? 'TBD' }}</span>
                                </div>

                                <div class="match-center"
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
                                                if (m >= 45) this.display += '+' + (m - 45); return;
                                            }
                                            if (this.phase === 'half_time') { this.period = 'HT'; this.display = 'HT'; return; }
                                            if (this.phase === 'second_half' && this.shs) {
                                                const s = Math.max(0, Math.floor((now - this.shs) / 1000));
                                                const m = Math.floor(s / 60); const sec = s % 60;
                                                const t = 45 + m;
                                                this.period = '2nd'; this.display = String(t).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                                                if (m >= 45) this.display += '+' + (m - 45); return;
                                            }
                                            if (this.phase === 'et_first_half' && this.et1s) {
                                                const s = Math.max(0, Math.floor((now - this.et1s) / 1000));
                                                const m = Math.floor(s / 60); const sec = s % 60;
                                                this.period = 'ET1'; this.display = String(90 + m).padStart(2,'0') + ':' + String(sec).padStart(2,'0'); return;
                                            }
                                            if (this.phase === 'et_second_half' && this.et2s) {
                                                const s = Math.max(0, Math.floor((now - this.et2s) / 1000));
                                                const m = Math.floor(s / 60); const sec = s % 60;
                                                this.period = 'ET2'; this.display = String(105 + m).padStart(2,'0') + ':' + String(sec).padStart(2,'0'); return;
                                            }
                                            this.period = ''; this.display = '—';
                                        },
                                        destroy() { if (this._id) clearInterval(this._id); }
                                    }"
                                    @endif
                                >
                                    @if($match->status === 'completed')
                                        <div class="match-score">{{ $match->score_team1 }} - {{ $match->score_team2 }}</div>
                                        <div class="match-status completed">{{ __('app.full_time') }}</div>
                                    @elseif($match->status === 'in_progress')
                                        <div class="match-score">{{ $match->score_team1 }} - {{ $match->score_team2 }}</div>
                                        <div class="match-status live">
                                            <span class="live-pulse d-inline-block rounded-circle bg-danger me-1" style="width:6px;height:6px;"></span>
                                            @if(in_array($match->phase, ['first_half','half_time','second_half','et_break','et_first_half','et_half_time','et_second_half']))
                                                <span x-text="period"></span> <span x-text="display"></span>
                                            @else
                                                {{ __('app.in_progress') }}
                                            @endif
                                        </div>
                                    @else
                                        <div class="match-time">{{ $match->match_date ? $match->match_date->format('H:i') : '--:--' }}</div>
                                        <div class="match-status scheduled">{{ __('app.scheduled') }}</div>
                                    @endif
                                </div>

                                <div class="match-team match-team-away">
                                    <span class="match-team-name">{{ $match->team2?->name ?? 'TBD' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="bi bi-calendar-x d-block"></i>
                        @if($this->filterMode === 'live')
                            <h4>{{ __('app.no_live_matches') }}</h4>
                        @elseif($this->filterMode === 'all')
                            <h4>{{ __('app.no_matches_yet') }}</h4>
                        @else
                            <h4>{{ __('app.no_matches_for_date') }}</h4>
                            <p>{{ __('app.try_another_date') }}</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Standings Tab --}}
            <div class="tab-pane fade" id="standings-tab" role="tabpanel">
                @if($standings)
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table standings-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('app.team') }}</th>
                                        <th class="text-center">{{ __('app.played') }}</th>
                                        <th class="text-center">{{ __('app.won') }}</th>
                                        <th class="text-center">{{ __('app.drawn') }}</th>
                                        <th class="text-center">{{ __('app.lost') }}</th>
                                        <th class="text-center">GF</th>
                                        <th class="text-center">GA</th>
                                        <th class="text-center">GD</th>
                                        <th class="text-center">{{ __('app.points') }}</th>
                                        <th class="text-center">{{ __('app.form') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($standings as $idx => $row)
                                        <tr class="{{ $idx < 3 ? 'table-highlight' : '' }}">
                                            <td class="fw-bold">{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="team-standings-dot {{ ['rank-gold', 'rank-silver', 'rank-bronze', ''][$idx] ?? '' }}"></span>
                                                    {{ $row['team_name'] }}
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $row['played'] }}</td>
                                            <td class="text-center">{{ $row['won'] }}</td>
                                            <td class="text-center">{{ $row['drawn'] }}</td>
                                            <td class="text-center">{{ $row['lost'] }}</td>
                                            <td class="text-center">{{ $row['goals_for'] }}</td>
                                            <td class="text-center">{{ $row['goals_against'] }}</td>
                                            <td class="text-center {{ $row['goal_difference'] > 0 ? 'text-success' : ($row['goal_difference'] < 0 ? 'text-danger' : '') }}">
                                                {{ $row['goal_difference'] > 0 ? '+' : '' }}{{ $row['goal_difference'] }}
                                            </td>
                                            <td class="text-center fw-bold fs-base">{{ $row['points'] }}</td>
                                            <td class="text-center">
                                                @foreach($row['form'] as $result)
                                                    <span class="form-badge form-{{ strtolower($result) }}">{{ $result }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-bar-chart d-block"></i>
                        <h4>{{ __('app.standings_not_available') }}</h4>
                        <p>{{ __('app.standings_will_appear_once_matches_played') }}</p>
                    </div>
                @endif
            </div>

            {{-- Teams Tab --}}
            <div class="tab-pane fade" id="teams-tab" role="tabpanel">
                <div class="row g-4">
                    @forelse($competition->teams as $team)
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('teams.show', $team->id) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm hover-lift h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="team-icon-lg mb-3">
                                            <i class="bi bi-shield-fill fs-2"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0">{{ $team->name }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="bi bi-people d-block"></i>
                                <h4>{{ __('app.no_teams_yet') }}</h4>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Top Scorers Tab --}}
            <div class="tab-pane fade" id="scorers-tab" role="tabpanel">
                @if($topScorers)
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table standings-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('app.player') }}</th>
                                        <th>{{ __('app.team') }}</th>
                                        <th class="text-center">{{ __('app.goals') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topScorers as $idx => $scorer)
                                        <tr>
                                            <td class="fw-bold">{{ $idx + 1 }}</td>
                                            <td>{{ $scorer->player_name }}</td>
                                            <td>{{ $scorer->team_name }}</td>
                                            <td class="text-center fw-bold fs-base text-gold">{{ $scorer->total_goals }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-crosshair d-block"></i>
                        <h4>{{ __('app.no_goals_yet') }}</h4>
                    </div>
                @endif
            </div>
        </div>

        {{-- Match Detail Modal --}}
        @if($selectedMatch)
            <div class="match-modal-backdrop" wire:click.self="closeMatchDetail">
                <div class="match-modal-dialog">
                    <div class="match-modal-content">
                        <button class="match-modal-close" wire:click="closeMatchDetail">
                            <i class="bi bi-x-lg"></i>
                        </button>

                        {{-- Scoreboard --}}
                        <div class="match-modal-scoreboard"
                            @if(in_array($selectedMatch->phase, ['first_half','half_time','second_half','et_break','et_first_half','et_half_time','et_second_half']))
                            x-data="{
                                phase: '{{ $selectedMatch->phase }}',
                                fhs: {{ $selectedMatch->first_half_started_at ? strtotime($selectedMatch->first_half_started_at) * 1000 : 'null' }},
                                shs: {{ $selectedMatch->second_half_started_at ? strtotime($selectedMatch->second_half_started_at) * 1000 : 'null' }},
                                et1s: {{ $selectedMatch->et_first_half_started_at ? strtotime($selectedMatch->et_first_half_started_at) * 1000 : 'null' }},
                                et2s: {{ $selectedMatch->et_second_half_started_at ? strtotime($selectedMatch->et_second_half_started_at) * 1000 : 'null' }},
                                period: '', display: '00:00', _id: null,
                                init() { this.tick(); this._id = setInterval(() => this.tick(), 1000); },
                                tick() {
                                    const now = Date.now();
                                    if (this.phase === 'full_time') { this.period = 'FT'; this.display = 'Full Time'; return; }
                                    if (this.phase === 'first_half' && this.fhs) {
                                        const s = Math.max(0, Math.floor((now - this.fhs) / 1000));
                                        const m = Math.floor(s / 60); const sec = s % 60;
                                        this.period = '1st Half'; this.display = String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                                        if (m >= 45) this.display += '+' + (m - 45); return;
                                    }
                                    if (this.phase === 'half_time') { this.period = 'Half Time'; this.display = 'HT'; return; }
                                    if (this.phase === 'second_half' && this.shs) {
                                        const s = Math.max(0, Math.floor((now - this.shs) / 1000));
                                        const m = Math.floor(s / 60); const sec = s % 60;
                                        this.period = '2nd Half'; this.display = String(45 + m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                                        if (m >= 45) this.display += '+' + (m - 45); return;
                                    }
                                    if (this.phase === 'et_first_half' && this.et1s) {
                                        const s = Math.max(0, Math.floor((now - this.et1s) / 1000));
                                        const m = Math.floor(s / 60); const sec = s % 60;
                                        this.period = 'ET 1st'; this.display = String(90 + m).padStart(2,'0') + ':' + String(sec).padStart(2,'0'); return;
                                    }
                                    if (this.phase === 'et_second_half' && this.et2s) {
                                        const s = Math.max(0, Math.floor((now - this.et2s) / 1000));
                                        const m = Math.floor(s / 60); const sec = s % 60;
                                        this.period = 'ET 2nd'; this.display = String(105 + m).padStart(2,'0') + ':' + String(sec).padStart(2,'0'); return;
                                    }
                                    this.period = '—'; this.display = '—';
                                },
                                destroy() { if (this._id) clearInterval(this._id); }
                            }"
                            @endif
                        >
                            <div class="scoreboard-row">
                                <div class="scoreboard-team scoreboard-home">
                                    <div class="team-shield">
                                        <i class="bi bi-shield-fill"></i>
                                    </div>
                                    <div class="team-name">{{ $selectedMatch->team1->name }}</div>
                                </div>
                                <div class="scoreboard-center">
                                    <div class="scoreboard-vs">VS</div>
                                    <div class="scoreboard-score">
                                        @if($selectedMatch->status === 'completed' || $selectedMatch->status === 'in_progress')
                                            <span class="score-value">{{ $selectedMatch->score_team1 }}</span>
                                            <span class="score-divider">:</span>
                                            <span class="score-value">{{ $selectedMatch->score_team2 }}</span>
                                        @else
                                            <span class="score-value score-tbd">--</span>
                                            <span class="score-divider">:</span>
                                            <span class="score-value score-tbd">--</span>
                                        @endif
                                    </div>
                                    @if($selectedMatch->status === 'completed')
                                        <span class="scoreboard-badge badge-ft">{{ __('app.full_time') }}</span>
                                    @elseif($selectedMatch->status === 'in_progress')
                                        <span class="scoreboard-badge badge-live">
                                            <span class="live-pulse d-inline-block rounded-circle bg-white me-1" style="width:6px;height:6px;"></span>
                                            <span x-text="period"></span> <span x-text="display"></span>
                                        </span>
                                    @else
                                        <span class="scoreboard-badge badge-scheduled">{{ __('app.scheduled') }}</span>
                                    @endif
                                    @if($selectedMatch->competition)
                                        <div class="scoreboard-competition">{{ $selectedMatch->competition->name }}</div>
                                    @endif
                                </div>
                                <div class="scoreboard-team scoreboard-away">
                                    <div class="team-shield">
                                        <i class="bi bi-shield-fill"></i>
                                    </div>
                                    <div class="team-name">{{ $selectedMatch->team2->name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="match-modal-body">
                            {{-- Sections Navigation --}}
                            @php
                                $hasEvents = $selectedMatch->events->count() > 0;
                                $hasLineups = $selectedMatch->lineups->count() > 0;
                                $hasStats = $selectedMatch->stats->count() > 0;
                            @endphp

                            @if($hasEvents || $hasLineups || $hasStats)
                                <ul class="match-detail-tabs" role="tablist">
                                    @if($hasEvents)
                                        <li><button class="tab-btn active" data-bs-toggle="tab" data-bs-target="#modal-events" type="button" role="tab"><i class="bi bi-list-ol"></i> {{ __('app.match_events') }}</button></li>
                                    @endif
                                    @if($hasLineups)
                                        <li><button class="tab-btn {{ !$hasEvents ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#modal-lineups" type="button" role="tab"><i class="bi bi-people"></i> {{ __('app.lineups') }}</button></li>
                                    @endif
                                    @if($hasStats)
                                        <li><button class="tab-btn {{ (!$hasEvents && !$hasLineups) ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#modal-stats" type="button" role="tab"><i class="bi bi-bar-chart"></i> {{ __('app.match_stats') }}</button></li>
                                    @endif
                                </ul>

                                <div class="tab-content mt-4">
                                    {{-- Events Tab --}}
                                    @if($hasEvents)
                                        <div class="tab-pane fade show active" id="modal-events" role="tabpanel">
                                            <div class="events-timeline">
                                                <div class="timeline-line"></div>
                                                @foreach($selectedMatch->events as $event)
                                                    @php
                                                        $isHome = $event->team_id === $selectedMatch->team1_id;
                                                        $eventColors = [
                                                            'goal' => ['icon' => 'bi-circle-fill', 'color' => '#00d4aa', 'bg' => 'rgba(0,212,170,.08)'],
                                                            'penalty_scored' => ['icon' => 'bi-circle-fill', 'color' => '#00d4aa', 'bg' => 'rgba(0,212,170,.08)'],
                                                            'own_goal' => ['icon' => 'bi-circle-fill', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.08)'],
                                                            'yellow_card' => ['icon' => 'bi-square-fill', 'color' => '#ffc107', 'bg' => 'rgba(255,193,7,.08)'],
                                                            'second_yellow' => ['icon' => 'bi-square-fill', 'color' => '#ffc107', 'bg' => 'rgba(255,193,7,.08)'],
                                                            'red_card' => ['icon' => 'bi-square-fill', 'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.08)'],
                                                            'substitution_in' => ['icon' => 'bi-arrow-repeat', 'color' => '#60a5fa', 'bg' => 'rgba(96,165,250,.08)'],
                                                            'substitution_out' => ['icon' => 'bi-arrow-repeat', 'color' => '#60a5fa', 'bg' => 'rgba(96,165,250,.08)'],
                                                            'assist' => ['icon' => 'bi-hand-index', 'color' => '#a78bfa', 'bg' => 'rgba(167,139,250,.08)'],
                                                        ];
                                                        $ec = $eventColors[$event->event_type] ?? ['icon' => 'bi-dot', 'color' => '#6b7280', 'bg' => 'transparent'];
                                                    @endphp
                                                    <div class="timeline-event {{ $isHome ? 'home' : 'away' }}">
                                                        <div class="timeline-dot" style="border-color: {{ $ec['color'] }};">
                                                            <i class="{{ $ec['icon'] }}" style="color: {{ $ec['color'] }};"></i>
                                                        </div>
                                                        <div class="timeline-card" style="background: {{ $ec['bg'] }}; border-left-color: {{ $ec['color'] }};">
                                                            <div class="timeline-card-header">
                                                                <span class="timeline-minute">{{ $event->minute }}{{ $event->added_time ? '+' . $event->added_time : '' }}'</span>
                                                                <span class="timeline-event-label">{{ __("app.event_{$event->event_type}") }}</span>
                                                            </div>
                                                            <div class="timeline-card-body">
                                                                <span class="timeline-player">{{ $event->player?->name }}</span>
                                                                @if($event->event_type === 'assist')
                                                                    <span class="timeline-assist">{{ __('app.assist') }}</span>
                                                                @elseif($event->event_type === 'own_goal')
                                                                    <span class="timeline-own-goal">{{ __('app.own_goal') }}</span>
                                                                @elseif($event->event_type === 'penalty_scored')
                                                                    <span class="timeline-penalty">(P)</span>
                                                                @elseif(in_array($event->event_type, ['substitution_in', 'substitution_out']) && $event->relatedPlayer)
                                                                    <span class="timeline-sub">↔ {{ $event->relatedPlayer->name }}</span>
                                                                @endif
                                                            </div>
                                                            @if($event->description)
                                                                <div class="timeline-card-desc">{{ $event->description }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Lineups Tab --}}
                                    @if($hasLineups)
                                        <div class="tab-pane fade {{ !$hasEvents ? 'show active' : '' }}" id="modal-lineups" role="tabpanel">
                                            <div class="row g-4">
                                                @php
                                                    $homeLineup = $selectedMatch->lineups->where('team_id', $selectedMatch->team1_id);
                                                    $awayLineup = $selectedMatch->lineups->where('team_id', $selectedMatch->team2_id);
                                                @endphp
                                                <div class="col-md-6">
                                                    <div class="lineup-team-header">
                                                        <div class="lineup-shield"><i class="bi bi-shield-fill text-primary"></i></div>
                                                        <h6>{{ $selectedMatch->team1->name }}</h6>
                                                    </div>
                                                    <div class="lineup-list">
                                                        @forelse($homeLineup as $lu)
                                                            <div class="lineup-player">
                                                                <span class="lineup-number">{{ $lu->jersey_number }}</span>
                                                                <span class="lineup-name">{{ $lu->player?->name ?? '—' }}</span>
                                                                @if($lu->is_captain)
                                                                    <span class="lineup-captain"><i class="bi bi-star-fill"></i></span>
                                                                @endif
                                                            </div>
                                                        @empty
                                                            <div class="text-chrome-muted fs-sm">—</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="lineup-team-header">
                                                        <div class="lineup-shield"><i class="bi bi-shield-fill text-success"></i></div>
                                                        <h6>{{ $selectedMatch->team2->name }}</h6>
                                                    </div>
                                                    <div class="lineup-list">
                                                        @forelse($awayLineup as $lu)
                                                            <div class="lineup-player">
                                                                <span class="lineup-number">{{ $lu->jersey_number }}</span>
                                                                <span class="lineup-name">{{ $lu->player?->name ?? '—' }}</span>
                                                                @if($lu->is_captain)
                                                                    <span class="lineup-captain"><i class="bi bi-star-fill"></i></span>
                                                                @endif
                                                            </div>
                                                        @empty
                                                            <div class="text-chrome-muted fs-sm">—</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Stats Tab --}}
                                    @if($hasStats)
                                        <div class="tab-pane fade {{ (!$hasEvents && !$hasLineups) ? 'show active' : '' }}" id="modal-stats" role="tabpanel">
                                            @php
                                                $homeStat = $selectedMatch->stats->where('team_id', $selectedMatch->team1_id)->first();
                                                $awayStat = $selectedMatch->stats->where('team_id', $selectedMatch->team2_id)->first();
                                            @endphp
                                            @if($homeStat && $awayStat)
                                                @php
                                                    $statPairs = [
                                                        ['label' => __('app.possession'), 'h' => $homeStat->possession, 'a' => $awayStat->possession, 'unit' => '%'],
                                                        ['label' => __('app.shots_total'), 'h' => $homeStat->shots_total, 'a' => $awayStat->shots_total],
                                                        ['label' => __('app.shots_on_target'), 'h' => $homeStat->shots_on_target, 'a' => $awayStat->shots_on_target],
                                                        ['label' => __('app.corners'), 'h' => $homeStat->corners, 'a' => $awayStat->corners],
                                                        ['label' => __('app.fouls'), 'h' => $homeStat->fouls, 'a' => $awayStat->fouls],
                                                        ['label' => __('app.yellow_cards'), 'h' => $homeStat->yellow_cards, 'a' => $awayStat->yellow_cards],
                                                        ['label' => __('app.red_cards'), 'h' => $homeStat->red_cards, 'a' => $awayStat->red_cards],
                                                    ];
                                                @endphp
                                                <div class="stats-compare">
                                                    @foreach($statPairs as $stat)
                                                        @php
                                                            $total = $stat['h'] + $stat['a'];
                                                            $hpct = $total > 0 ? round($stat['h'] / $total * 100) : 50;
                                                        @endphp
                                                        <div class="stat-row">
                                                            <div class="stat-label">{{ $stat['label'] }}</div>
                                                            <div class="stat-bars">
                                                                <div class="stat-bar-group">
                                                                    <span class="stat-val stat-val-home">{{ $stat['h'] }}{{ $stat['unit'] ?? '' }}</span>
                                                                    <div class="stat-track">
                                                                        <div class="stat-fill stat-fill-home" style="width: {{ $hpct }}%"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="stat-bar-group">
                                                                    <div class="stat-track">
                                                                        <div class="stat-fill stat-fill-away" style="width: {{ 100 - $hpct }}%"></div>
                                                                    </div>
                                                                    <span class="stat-val stat-val-away">{{ $stat['a'] }}{{ $stat['unit'] ?? '' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="empty-state py-4">
                                    <i class="bi bi-info-circle d-block fs-2 text-chrome-muted"></i>
                                    <p class="text-chrome-muted mt-2">{{ __('app.no_match_details') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

