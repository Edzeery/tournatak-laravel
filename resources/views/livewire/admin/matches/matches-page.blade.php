<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
<div wire:poll.15s>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-calendar-event-fill text-gold"></i>
                {{ __('app.match_management') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ __('app.matches_desc') }}</p>
        </div>
        <a href="{{ route('admin.matches.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_match') }}
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                    class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.matches') }}</li>
        </ol>
    </nav>

    {{-- Filters --}}
    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_matches_placeholder') }}"
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold fs-base">{{ __('app.status') }}</label>
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="scheduled">{{ __('app.scheduled') }}</option>
                        <option value="in_progress">{{ __('app.in_progress') }}</option>
                        <option value="completed">{{ __('app.completed') }}</option>
                        <option value="postponed">{{ __('app.postponed') }}</option>
                        <option value="cancelled">{{ __('app.cancelled') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold fs-base">{{ __('app.per_page') }}</label>
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <label class="form-label fw-bold fs-base w-100">&nbsp;</label>
                    <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('app.reset') }}
                    </button>
                </div>
            </div>

            {{-- Status quick filter tabs --}}
            <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                <button class="btn btn-sm {{ empty($statusFilter) ? 'btn-dark' : 'btn-outline-secondary' }}"
                    wire:click="$set('statusFilter', '')">{{ __('app.all') }}</button>
                <button class="btn btn-sm {{ $statusFilter === 'scheduled' ? 'btn-primary' : 'btn-outline-primary' }}"
                    wire:click="$set('statusFilter', 'scheduled')">
                    <i class="bi bi-calendar3"></i> {{ __('app.scheduled') }}
                </button>
                <button class="btn btn-sm {{ $statusFilter === 'in_progress' ? 'btn-danger' : 'btn-outline-danger' }}"
                    wire:click="$set('statusFilter', 'in_progress')">
                    <span class="pulse-dot d-inline-block rounded-circle bg-danger me-1"
                        style="width:6px;height:6px;"></span> {{ __('app.live') }}
                </button>
                <button class="btn btn-sm {{ $statusFilter === 'completed' ? 'btn-success' : 'btn-outline-success' }}"
                    wire:click="$set('statusFilter', 'completed')">
                    <i class="bi bi-check-circle"></i> {{ __('app.completed') }}
                </button>
                <button class="btn btn-sm {{ $statusFilter === 'postponed' ? 'btn-warning' : 'btn-outline-warning' }}"
                    wire:click="$set('statusFilter', 'postponed')">
                    <i class="bi bi-clock"></i> {{ __('app.postponed') }}
                </button>
                <button
                    class="btn btn-sm {{ $statusFilter === 'cancelled' ? 'btn-secondary' : 'btn-outline-secondary' }}"
                    wire:click="$set('statusFilter', 'cancelled')">
                    <i class="bi bi-x-circle"></i> {{ __('app.cancelled') }}
                </button>
            </div>
        </div>
    </div>

    <div wire:loading>
        <x-skeleton type="table" :rows="5" />
    </div>

    <div class="card border-0" wire:loading.remove>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-chrome-muted fs-xs" style="width:50px;">#</th>
                            <th class="fs-xs text-uppercase text-chrome-muted">{{ __('app.competition') }}</th>
                            <th class="fs-xs text-uppercase text-chrome-muted">{{ __('app.matchup') }}</th>
                            <th class="fs-xs text-uppercase text-chrome-muted">{{ __('app.date') }}</th>
                            <th class="text-center fs-xs text-uppercase text-chrome-muted" style="width:90px;">
                                {{ __('app.status') }}</th>
                            <th class="text-center fs-xs text-uppercase text-chrome-muted" style="width:200px;">
                                {{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matches as $match)
                            <tr wire:key="match-{{ $match->id }}">
                                <td class="text-chrome-muted fs-sm">{{ $match->id }}</td>

                                {{-- Competition --}}
                                <td>
                                    <div class="fw-semibold fs-sm">{{ $match->competition->name ?? '-' }}</div>
                                    @if ($match->round)
                                        <div class="text-chrome-muted fs-xs">{{ $match->round }}</div>
                                    @endif
                                </td>

                                {{-- Matchup --}}
                                <td>
                                    <div class="d-flex align-items-center gap-3" style="min-width:260px;"
                                        @if(in_array($match->phase, ['first_half','half_time','second_half','et_break','et_first_half','et_half_time','et_second_half']))
                                        x-data="{
                                            phase: '{{ $match->phase }}',
                                            fhs: {{ $match->first_half_started_at ? strtotime($match->first_half_started_at) * 1000 : 'null' }},
                                            shs: {{ $match->second_half_started_at ? strtotime($match->second_half_started_at) * 1000 : 'null' }},
                                            et1s: {{ $match->et_first_half_started_at ? strtotime($match->et_first_half_started_at) * 1000 : 'null' }},
                                            et2s: {{ $match->et_second_half_started_at ? strtotime($match->et_second_half_started_at) * 1000 : 'null' }},
                                            at1: {{ $match->added_time_first_half ?? 0 }},
                                            at2: {{ $match->added_time_second_half ?? 0 }},
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
                                                if (this.phase === 'half_time' && this.fhs) {
                                                    this.period = 'HT'; this.display = 'HT';
                                                    return;
                                                }
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
                                        {{-- Team 1 --}}
                                        <div class="d-flex align-items-center gap-2 text-end" style="flex:1;">
                                            <span class="fw-semibold text-truncate d-none d-md-inline fs-sm"
                                                style="max-width:80px;">
                                                {{ $match->team1->name ?? '?' }}
                                            </span>
                                            @if ($match->team1->logo)
                                                <img src="{{ $match->team1->logo_url }}"
                                                    alt="{{ $match->team1->name }}"
                                                    class="rounded-circle object-cover border-chrome flex-shrink-0 logo-ring w-38 h-38">
                                            @else
                                                <div
                                                    class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold bg-opacity-10 flex-shrink-0 fs-base w-38 h-38">
                                                    {{ mb_substr($match->team1->name ?? '?', 0, 1) }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Score / Timer --}}
                                        <div class="text-center flex-shrink-0" style="min-width:80px;">
                                            @if($match->status === 'in_progress')
                                                <div class="fw-bold fs-base text-danger">{{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}</div>
                                                <div class="fs-xs fw-semibold text-danger mt-1">
                                                    <span class="pulse-dot rounded-circle bg-danger d-inline-block me-1" style="width:5px;height:5px;"></span>
                                                    <span x-text="period"></span>
                                                    <span class="text-chrome-muted mx-1">·</span>
                                                    <span x-text="display"></span>
                                                </div>
                                            @elseif($match->status === 'completed')
                                                <div class="fw-bold fs-base text-theme-primary">
                                                    {{ $match->score_team1 }} - {{ $match->score_team2 }}
                                                </div>
                                                @if($match->match_date)
                                                    <div class="fs-xs text-chrome-muted mt-1">{{ $match->match_date->format('d M') }}</div>
                                                @endif
                                            @else
                                                <div class="text-chrome-muted fw-semibold fs-sm">VS</div>
                                                @if ($match->match_date)
                                                    <div class="fs-xs text-chrome-muted mt-1">
                                                        {{ $match->match_date->format('d M') }}</div>
                                                @endif
                                            @endif
                                        </div>

                                        {{-- Team 2 --}}
                                        <div class="d-flex align-items-center gap-2" style="flex:1;">
                                            @if ($match->team2->logo)
                                                <img src="{{ $match->team2->logo_url }}"
                                                    alt="{{ $match->team2->name }}"
                                                    class="rounded-circle object-cover border-chrome flex-shrink-0 logo-ring w-38 h-38">
                                            @else
                                                <div
                                                    class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold bg-opacity-10 flex-shrink-0 fs-base w-38 h-38">
                                                    {{ mb_substr($match->team2->name ?? '?', 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="fw-semibold text-truncate d-none d-md-inline fs-sm"
                                                style="max-width:80px;">
                                                {{ $match->team2->name ?? '?' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td>
                                    @if ($match->match_date)
                                        <div class="fw-semibold fs-sm">{{ $match->match_date->format('Y-m-d') }}</div>
                                        <div class="text-chrome-muted fs-xs">
                                            <i class="bi bi-clock"></i> {{ $match->match_date->format('H:i') }}
                                        </div>
                                    @else
                                        <span class="text-chrome-muted">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="text-center">
                                    <x-status-badge domain="match" :status="$match->status" set="bi" />
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        @if($match->status === 'scheduled' || $match->status === 'pending')
                                            <button class="btn btn-sm btn-outline-danger rounded-md"
                                                wire:click="startMatch({{ $match->id }})"
                                                wire:confirm="{{ __('app.confirm_start_match') }}"
                                                title="{{ __('app.start_match') }}">
                                                <i class="bi bi-play-fill"></i>
                                            </button>
                                        @elseif($match->status === 'in_progress')
                                            <button class="btn btn-sm btn-outline-dark rounded-md"
                                                wire:click="endMatch({{ $match->id }})"
                                                wire:confirm="{{ __('app.confirm_end_match') }}"
                                                title="{{ __('app.end_match') }}">
                                                <i class="bi bi-stop-fill"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.matches.control', $match) }}"
                                            class="btn btn-sm btn-outline-{{ $match->status === 'in_progress' ? 'danger' : 'dark' }} rounded-md"
                                            title="{{ __('app.match_control') }}">
                                            <i class="bi bi-controller"></i>
                                        </a>
                                        <a href="{{ route('admin.matches.edit', $match) }}"
                                            class="btn btn-sm btn-outline-primary rounded-md"
                                            title="{{ __('app.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin.matches.lineup', $match->id) }}"
                                            class="btn btn-sm btn-outline-success rounded-md"
                                            title="{{ __('app.lineup') }}">
                                            <i class="bi bi-people-fill"></i>
                                        </a>
                                        <a href="{{ route('admin.matches.events', $match->id) }}"
                                            class="btn btn-sm btn-outline-warning rounded-md"
                                            title="{{ __('app.events') }}">
                                            <i class="bi bi-clock-history"></i>
                                        </a>
                                        <a href="{{ route('admin.matches.stats', $match->id) }}"
                                            class="btn btn-sm btn-outline-info rounded-md"
                                            title="{{ __('app.match_stats') }}">
                                            <i class="bi bi-bar-chart-line"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger rounded-md"
                                            wire:click="delete({{ $match->id }})"
                                            wire:confirm="{{ __('app.confirm_delete_match') }}"
                                            title="{{ __('app.delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-empty-state icon="bi-calendar2-event" title="{{ __('app.matches') }}"
                                        message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $matches->links() }}</div>
</div>
