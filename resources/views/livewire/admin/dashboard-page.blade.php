<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
<div wire:poll.15s>
            <h4 class="fw-bold mb-1 text-dark-theme">
                <i class="bi bi-grid-1x2-fill text-gold"></i> {{ __('app.dashboard') }}
            </h4>
            <p class="text-muted mb-0 section-desc">{{ __('app.admin_dashboard_desc') }}</p>
        </div>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-md">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                    class="text-decoration-none breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.home') }}</li>
        </ol>
    </nav>

    {{-- Main Stats Cards --}}
    <div class="row g-3 mb-4 stagger-children">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue-bright">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['users'] }}</div>
                <div class="stat-label">{{ __('app.user') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-gold bg-opacity-10">
                    <i class="bi bi-trophy-fill text-gold"></i>
                </div>
                <div class="stat-number">{{ $stats['competitions'] }}</div>
                <div class="stat-label">{{ __('app.stat_competition') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <i class="bi bi-shield-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['teams'] }}</div>
                <div class="stat-label">{{ __('app.stat_team') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['players'] }}</div>
                <div class="stat-label">{{ __('app.stat_player') }}</div>
            </div>
        </div>
    </div>

    {{-- Match Stats Row --}}
    <div class="row g-3 mb-4 stagger-children">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon stat-icon-amber">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['matches'] }}</div>
                <div class="stat-label">{{ __('app.match') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon stat-icon-red">
                    <i class="bi bi-circle-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['goals'] }}</div>
                <div class="stat-label">{{ __('app.goals') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon stat-icon-cyan">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['staff'] }}</div>
                <div class="stat-label">{{ __('app.staff_member') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon stat-icon-dark-red">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['injuries'] }}</div>
                <div class="stat-label">{{ __('app.active_injury') }}</div>
            </div>
        </div>
    </div>

    {{-- Registration Stats Row --}}
    <div class="row g-3 mb-4 stagger-children">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue-bright">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['registrations_pending'] }}</div>
                <div class="stat-label">{{ __('app.registrations_pending') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['registrations_approved'] }}</div>
                <div class="stat-label">{{ __('app.registrations_approved') }}</div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 animate-fade-in-up animate-delay-1">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 section-title-dark">
                        <i class="bi bi-bar-chart-fill text-gold"></i> {{ __('app.monthly_goals') }}
                    </h6>
                    <div id="chart-monthly-goals" class="min-vh-260"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 animate-fade-in-up animate-delay-2">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 section-title-dark">
                        <i class="bi bi-pie-chart-fill text-gold"></i> {{ __('app.match_status_dist') }}
                    </h6>
                    <div id="chart-match-status" class="min-vh-260"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card border-0 mb-4 animate-fade-in-up animate-delay-2">
        <div class="card-body">
            <h6 class="fw-bold mb-3 section-title-dark">
                <i class="bi bi-lightning-fill text-gold"></i> {{ __('app.quick_actions') }}
            </h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.teams.create') }}" class="btn btn-sm btn-outline-success rounded-md">
                    <i class="bi bi-plus-lg"></i> {{ __('app.new_team') }}
                </a>
                <a href="{{ route('admin.players.create') }}" class="btn btn-sm btn-outline-primary rounded-md">
                    <i class="bi bi-plus-lg"></i> {{ __('app.new_player') }}
                </a>
                <a href="{{ route('admin.matches.create') }}" class="btn btn-sm btn-outline-warning rounded-md">
                    <i class="bi bi-plus-lg"></i> {{ __('app.new_match') }}
                </a>
                <a href="{{ route('admin.competitions.create') }}" class="btn btn-sm btn-outline-info rounded-md">
                    <i class="bi bi-plus-lg"></i> {{ __('app.new_competition') }}
                </a>
                <a href="{{ route('admin.positions.index') }}" class="btn btn-sm btn-outline-secondary rounded-md">
                    <i class="bi bi-geo-alt"></i> {{ __('app.manage_positions') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Matches --}}
        <div class="col-lg-8">
            @if ($recentMatches->count())
                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 section-title-dark">
                            <i class="bi bi-calendar-event text-gold"></i> {{ __('app.recent_matches') }}
                        </h6>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="fs-xs">{{ __('app.header_team1') }}</th>
                                        <th class="text-center fs-xs">{{ __('app.score') }}</th>
                                        <th class="fs-xs">{{ __('app.header_team2') }}</th>
                                        <th class="fs-xs">{{ __('app.date') }}</th>
                                        <th class="fs-xs">{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentMatches as $match)
                                    <tr wire:key="rm-{{ $match->id }}"
                                        x-data="{
                                            phase: '{{ $match->phase }}',
                                            fhs: {{ $match->first_half_started_at ? strtotime($match->first_half_started_at) * 1000 : 'null' }},
                                            shs: {{ $match->second_half_started_at ? strtotime($match->second_half_started_at) * 1000 : 'null' }},
                                            et1s: {{ $match->et_first_half_started_at ? strtotime($match->et_first_half_started_at) * 1000 : 'null' }},
                                            et2s: {{ $match->et_second_half_started_at ? strtotime($match->et_second_half_started_at) * 1000 : 'null' }},
                                            period: '', display: '—', _id: null,
                                            init() {
                                                if (['first_half','half_time','second_half','et_break','et_first_half','et_half_time','et_second_half'].includes(this.phase)) {
                                                    this.tick();
                                                    this._id = setInterval(() => this.tick(), 1000);
                                                }
                                            },
                                            tick() {
                                                const now = Date.now();
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
                                                if (this.phase === 'et_break') { this.period = 'ET'; this.display = 'Break'; return; }
                                                if (this.phase === 'et_first_half' && this.et1s) {
                                                    const s = Math.max(0, Math.floor((now - this.et1s) / 1000));
                                                    const m = Math.floor(s / 60); const sec = s % 60;
                                                    this.period = 'ET1'; this.display = String(90 + m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                                                    return;
                                                }
                                                if (this.phase === 'et_half_time') { this.period = 'ET'; this.display = 'HT'; return; }
                                                if (this.phase === 'et_second_half' && this.et2s) {
                                                    const s = Math.max(0, Math.floor((now - this.et2s) / 1000));
                                                    const m = Math.floor(s / 60); const sec = s % 60;
                                                    this.period = 'ET2'; this.display = String(105 + m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                                                    return;
                                                }
                                                if (this.phase === 'full_time') { this.period = 'FT'; this.display = 'FT'; return; }
                                                this.period = ''; this.display = '—';
                                            },
                                            destroy() { if (this._id) clearInterval(this._id); }
                                        }"
                                    >
                                        <td class="fw-bold fs-md">
                                            {{ $match->team1->name ?? '—' }}</td>
                                        <td class="text-center">
                                            @if ($match->status === 'completed')
                                                <span class="badge bg-gold rounded-pill px-3 fs-md">
                                                    {{ $match->score_team1 ?? 0 }} -
                                                    {{ $match->score_team2 ?? 0 }}
                                                </span>
                                            @elseif ($match->status === 'in_progress')
                                                <span class="badge bg-danger rounded-pill px-3 fs-md">
                                                    {{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}
                                                </span>
                                                <div class="fs-xs text-danger mt-1">
                                                    <span x-text="period"></span> <span x-text="display"></span>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary rounded-pill badge-count">
                                                    {{ __('app.status_scheduled') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="fw-bold fs-md">
                                            {{ $match->team2->name ?? '—' }}</td>
                                            <td class="activity-date">
                                                {{ formatDate($match->match_date, 'd/m') ?? '—' }}</td>
                                            <td class="text-center d-flex flex-wrap gap-2">
                                                <a href="{{ route('admin.matches.lineup', $match) }}"
                                                    class="btn btn-sm btn-outline-success action-btn-sm"
                                                    title="{{ __('app.lineup') }}">
                                                    <i class="bi bi-people-fill"></i>
                                                </a>
                                                <a href="{{ route('admin.matches.events', $match) }}"
                                                    class="btn btn-sm btn-outline-warning action-btn-sm"
                                                    title="{{ __('app.events') }}">
                                                    <i class="bi bi-clock-history"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Top Scorers --}}
        <div class="col-lg-4">
            @if ($topScorers->count())
                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 section-title-dark">
                            <i class="bi bi-award-fill text-gold"></i> {{ __('app.top_scorers') }}
                        </h6>
                        @foreach ($topScorers as $idx => $scorer)
                            <div
                                class="d-flex align-items-center gap-3 {{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
                                <span
                                    class="badge bg-{{ $idx === 0 ? 'warning text-dark' : ($idx === 1 ? 'secondary' : 'dark') }} rounded-circle scorer-rank">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-bold item-name">
                                        {{ $scorer->player->name ?? '—' }}</div>
                                    <small class="text-muted item-sub">{{ $scorer->player->team->name ?? '' }}</small>
                                </div>
                                <span class="badge bg-danger rounded-pill fs-sm">{{ $scorer->goals }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="card border-0 mt-4" wire:loading.opacity>
        <div class="card-body">
            <h6 class="fw-bold mb-3 section-title-dark">
                <i class="bi bi-clock-history text-gold"></i> {{ __('app.latest_activity') }}
            </h6>
            @if ($activities->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="fs-xs">#</th>
                                <th class="fs-xs">{{ __('app.event') }}</th>
                                <th class="fs-xs">{{ __('app.by') }}</th>
                                <th class="fs-xs">{{ __('app.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $activity)
                                <tr wire:key="{{ $activity->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold activity-cell">{{ $activity->description }}</td>
                                    <td class="fw-bold activity-cell">{{ $activity->user?->name }}</td>
                                    <td class="activity-date">
                                        {{ $activity->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state py-3">
                    <i class="bi bi-clock-history d-block empty-icon-lg"></i>
                    <h5>{{ __('app.no_activity_yet') }}</h5>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Registrations --}}
    <div class="row g-4 mt-2">
        <div class="col-12">
            @if ($recentRegistrations->count())
                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 section-title-dark">
                            <i class="bi bi-person-plus-fill text-gold"></i> {{ __('app.recent_registrations') }}
                        </h6>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="fs-xs">{{ __('app.competition') }}</th>
                                        <th class="fs-xs">{{ __('app.participant') }}</th>
                                        <th class="fs-xs">{{ __('app.type') }}</th>
                                        <th class="fs-xs">{{ __('app.status') }}</th>
                                        <th class="fs-xs">{{ __('app.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentRegistrations as $reg)
                                        <tr>
                                            <td class="fw-bold fs-md">{{ $reg->competition->name ?? '—' }}</td>
                                            <td class="fw-bold fs-md">{{ $reg->getParticipantName() ?? '—' }}</td>
                                            <td>
                                                @if ($reg->isIndividualRegistration())
                                                    <span class="badge bg-info-subtle text-info fs-sm">{{ __('app.participant_type_individual') }}</span>
                                                @else
                                                    <span class="badge bg-primary-subtle text-primary fs-sm">{{ __('app.team') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <x-status-badge domain="general" class="bg-success-subtle text-success " status="{{ $reg->status }}" set="bi" />
 
                                            </td>
                                            <td class="text-theme-muted fs-xs">{{ formatDate($reg->created_at) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ✅ خارج أي حلقة، يُنفَّذ مرة واحدة فقط لكل تحميل صفحة --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ApexCharts === 'undefined') return;

            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const textColor = isDark ? '#c1c1c1' : '#6c757d';
            const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

            ApexCharts.exec('chart-monthly-goals', 'destroy');
            ApexCharts.exec('chart-match-status', 'destroy');

            var monthlyChart = new ApexCharts(document.querySelector('#chart-monthly-goals'), {
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit'
                },
                series: [{
                    name: '{{ __('app.goals') }}',
                    data: @js(array_values($monthlyGoals ?: []))
                }],
                xaxis: {
                    categories: @php
                        $mLabels = [];
                        for ($m = 1; $m <= 12; $m++) {
                            $mLabels[] = Carbon\Carbon::create()
                                ->month($m)
                                ->locale(app()->getLocale())->translatedFormat('M');
                        }
                    @endphp @js($mLabels),
                    labels: {
                        style: {
                            colors: textColor
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: textColor
                        }
                    }
                },
                grid: {
                    borderColor: gridColor
                },
                colors: ['#ffc107'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '50%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light'
                },
            });
            monthlyChart.render();

            @php
                $sLabels = [
                    'completed' => __('app.status_completed'),
                    'in_progress' => __('app.status_in_progress'),
                    'scheduled' => __('app.status_scheduled'),
                    'upcoming' => __('app.upcoming'),
                ];

                $statusValues = array_values($matchStatuses->toArray());
                $statusLabelsMapped = array_map(function ($k) use ($sLabels) {
                    return $sLabels[$k] ?? $k;
                }, array_keys($matchStatuses->toArray()));
            @endphp

            var matchStatusChart = new ApexCharts(document.querySelector('#chart-match-status'), {
                chart: {
                    type: 'donut',
                    height: 260,
                    fontFamily: 'inherit'
                },
                series: @js($statusValues),
                labels: @js($statusLabelsMapped),
                colors: ['#16a34a', '#f59e0b', '#3b82f6'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: '{{ __('app.total') }}'
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light'
                },
            });
            matchStatusChart.render();
        });
    </script>
@endpush
