<div wire:poll.10s>
    {{-- Scoreboard Hero --}}
    <section class="live-scoreboard">
        <div class="live-scoreboard-bg"></div>
        <div class="container position-relative">
            <div class="live-competition-badge">
                <i class="bi bi-trophy-fill me-1"></i>
                {{ $match->competition?->name ?? __('app.competition') }}
            </div>

            <div class="live-scoreboard-main">
                <div class="live-team live-team-home">
                    <div class="live-team-name">{{ $match->team1?->name ?? 'TBD' }}</div>
                    <div class="live-team-score">{{ $match->score_team1 ?? 0 }}</div>
                </div>

                <div class="live-center">
                    <div class="live-vs">VS</div>
                    @if($match->status === 'in_progress')
                        <div class="live-status-badge">
                            <span class="live-pulse d-inline-block rounded-circle bg-danger me-1" style="width:8px;height:8px;"></span>
                            @if(in_array($match->phase, ['first_half','half_time','second_half','et_break','et_first_half','et_half_time','et_second_half']))
                                <span class="live-timer"
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
                                >
                                    <span x-text="period" class="live-period"></span>
                                    <span x-text="display" class="live-clock"></span>
                                </span>
                            @else
                                <span class="live-text">{{ __('app.in_progress') }}</span>
                            @endif
                        </div>
                    @elseif($match->status === 'completed')
                        <div class="live-status-badge completed">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ __('app.full_time') }}
                        </div>
                    @else
                        <div class="live-status-badge scheduled">
                            <i class="bi bi-clock me-1"></i>
                            {{ $match->match_date?->format('H:i') }}
                        </div>
                    @endif

                    @if($match->match_date)
                        <div class="live-date">{{ $match->match_date->format('d M Y') }}</div>
                    @endif
                    @if($match->venue)
                        <div class="live-venue"><i class="bi bi-geo-alt me-1"></i>{{ $match->venue }}</div>
                    @endif
                </div>

                <div class="live-team live-team-away">
                    <div class="live-team-name">{{ $match->team2?->name ?? 'TBD' }}</div>
                    <div class="live-team-score">{{ $match->score_team2 ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="live-scoreboard-bottom"></div>
    </section>

    {{-- Content --}}
    <div class="container py-4">

        {{-- Video Embed Placeholder --}}
        <div class="live-video-placeholder mb-4">
            <div class="live-video-inner">
                <i class="bi bi-play-circle"></i>
                <span>{{ __('app.video_stream_coming_soon') }}</span>
            </div>
        </div>

        <div class="row g-4">
            {{-- Main: Events Timeline --}}
            <div class="col-lg-8">
                <div class="live-card">
                    <div class="live-card-header">
                        <i class="bi bi-lightning-fill"></i>
                        {{ __('app.match_events') }}
                    </div>
                    <div class="live-card-body p-0">
                        @if($match->events->count())
                            <div class="live-timeline">
                                @php
                                    $allEvents = $match->events->sortBy(fn($e) => $e->minute * 60 + ($e->added_time ?? 0));
                                @endphp
                                @foreach($allEvents as $event)
                                    @php
                                        $isHome = $event->team_id === $match->team1_id;
                                        $icon = match($event->event_type) {
                                            'goal', 'penalty_scored' => 'bi-soccer-ball',
                                            'own_goal' => 'bi-soccer-ball text-danger',
                                            'yellow_card', 'second_yellow' => 'bi-square-fill text-warning',
                                            'red_card' => 'bi-square-fill text-danger',
                                            'substitution_in', 'substitution_out' => 'bi-arrow-repeat',
                                            'injury' => 'bi-activity',
                                            'save' => 'bi-hand-index',
                                            'assist' => 'bi-eye',
                                            default => 'bi-circle-fill',
                                        };
                                        $label = match($event->event_type) {
                                            'goal' => __('app.goal'),
                                            'own_goal' => __('app.own_goal'),
                                            'penalty_scored' => __('app.penalty'),
                                            'yellow_card' => __('app.yellow_card'),
                                            'second_yellow' => __('app.second_yellow'),
                                            'red_card' => __('app.red_card'),
                                            'substitution_in' => __('app.substitution'),
                                            'substitution_out' => '',
                                            'injury' => __('app.injury'),
                                            'save' => __('app.save'),
                                            'assist' => __('app.assist'),
                                            default => '',
                                        };
                                        $minuteDisplay = $event->minute . ($event->added_time ? "+{$event->added_time}" : "'");
                                    @endphp
                                    <div class="live-timeline-item {{ $isHome ? 'home' : 'away' }}">
                                        <div class="timeline-dot {{ $isHome ? 'home' : 'away' }}">
                                            <i class="bi {{ $icon }}"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-minute">{{ $minuteDisplay }}</div>
                                            <div class="timeline-desc">
                                                <strong>{{ $event->player?->name ?? '—' }}</strong>
                                                @if($label)
                                                    <span class="timeline-label">{{ $label }}</span>
                                                @endif
                                                @if($event->relatedPlayer)
                                                    <span class="timeline-related">
                                                        <i class="bi bi-arrow-right"></i> {{ $event->relatedPlayer->name }}
                                                    </span>
                                                @endif
                                                @if($event->description)
                                                    <div class="timeline-note">{{ $event->description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 text-chrome-muted">
                                <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                                <span>{{ __('app.no_events_yet') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar: Lineups + Stats --}}
            <div class="col-lg-4">
                {{-- Match Info --}}
                <div class="live-card mb-4">
                    <div class="live-card-header">
                        <i class="bi bi-info-circle-fill"></i>
                        {{ __('app.match_info') }}
                    </div>
                    <div class="live-card-body small">
                        <div class="live-info-row">
                            <span>{{ __('app.competition') }}</span>
                            <span>{{ $match->competition?->name ?? '—' }}</span>
                        </div>
                        @if($match->referee)
                            <div class="live-info-row">
                                <span>{{ __('app.referee') }}</span>
                                <span>{{ $match->referee }}</span>
                            </div>
                        @endif
                        @if($match->venue)
                            <div class="live-info-row">
                                <span>{{ __('app.venue') }}</span>
                                <span>{{ $match->venue }}</span>
                            </div>
                        @endif
                        @if($match->attendance)
                            <div class="live-info-row">
                                <span>{{ __('app.attendance') }}</span>
                                <span>{{ number_format($match->attendance) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Stats --}}
                @if($team1Stats && $team2Stats)
                    <div class="live-card mb-4">
                        <div class="live-card-header">
                            <i class="bi bi-bar-chart-fill"></i>
                            {{ __('app.match_stats') }}
                        </div>
                        <div class="live-card-body">
                            @foreach(['possession', 'shots_total', 'shots_on_target', 'corners', 'fouls', 'yellow_cards', 'red_cards', 'offsides', 'saves'] as $stat)
                                @php
                                    $v1 = $team1Stats->$stat ?? 0;
                                    $v2 = $team2Stats->$stat ?? 0;
                                    $total = $v1 + $v2;
                                    $pct1 = $total > 0 ? round($v1 / $total * 100) : 50;
                                @endphp
                                <div class="live-stat-row">
                                    <span class="live-stat-val home">{{ $stat === 'possession' ? number_format($v1, 1) : $v1 }}</span>
                                    <div class="live-stat-bar">
                                        <div class="live-stat-label">{{ __("app.stat_{$stat}") }}</div>
                                        <div class="live-stat-track">
                                            <div class="live-stat-fill home" style="width:{{ $pct1 }}%"></div>
                                        </div>
                                    </div>
                                    <span class="live-stat-val away">{{ $stat === 'possession' ? number_format($v2, 1) : $v2 }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Lineups Accordion --}}
                <div class="live-card">
                    <div class="live-card-header">
                        <i class="bi bi-people-fill"></i>
                        {{ __('app.lineups') }}
                    </div>
                    <div class="live-card-body p-0">
                        @foreach([
                            ['team' => $match->team1, 'lineup' => $team1Lineup],
                            ['team' => $match->team2, 'lineup' => $team2Lineup],
                        ] as $side)
                            <div class="live-lineup-team">
                                <div class="live-lineup-team-header collapsed" data-bs-toggle="collapse" data-bs-target="#lineup-{{ $side['team']?->id }}" role="button">
                                    <span>{{ $side['team']?->name ?? '—' }}</span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="collapse" id="lineup-{{ $side['team']?->id }}">
                                    <div class="live-lineup-players">
                                        @forelse($side['lineup'] as $lp)
                                            <div class="live-lineup-player {{ $lp->is_starter ? '' : 'sub' }}">
                                                <span class="lp-jersey">{{ $lp->jersey_number ?? '—' }}</span>
                                                <span class="lp-name">{{ $lp->player?->name ?? '—' }}</span>
                                                <span class="lp-pos">{{ $lp->position?->name ?? '' }}</span>
                                                @if($lp->is_captain)
                                                    <i class="bi bi-star-fill lp-captain" title="{{ __('app.captain') }}"></i>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="text-center py-3 text-chrome-muted small">{{ __('app.no_lineup') }}</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
