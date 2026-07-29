<div>
    <section class="hero-sports hero-sports-sm text-white">
        <div class="container hero-content">
            <div class="text-center position-relative">
                <div class="hero-badge mx-auto mb-3 d-inline-flex">
                    <i class="bi bi-calendar-event-fill"></i> {{ __('app.all_matches') }}
                </div>
                <h1 class="fw-bold mb-3 fs-4xl">{{ __('app.matches_hero_title') }}</h1>
                <p class="text-theme-muted hero-desc">
                    {{ __('app.matches_hero_desc') }}
                </p>
            </div>
        </div>
        <div class="hero-gradient-bottom"></div>
    </section>

    <div class="container py-5 mt-neg-20">
        {{-- Filters Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                {{-- Filter Mode Tabs --}}
                <div class="matches-filters mb-3">
                    <button class="filter-btn {{ $this->filterMode === 'date' ? 'active' : '' }}" wire:click="setFilter('date')">{{ __('app.filter_date') }}</button>
                    <button class="filter-btn {{ $this->filterMode === 'all' ? 'active' : '' }}" wire:click="setFilter('all')">{{ __('app.all') }}</button>
                    <button class="filter-btn {{ $this->filterMode === 'live' ? 'active' : '' }}" wire:click="setFilter('live')">{{ __('app.live') }}</button>
                </div>

                @if($this->filterMode === 'date')
                    {{-- Date Bar --}}
                    <div class="date-bar mb-3">
                        <button class="date-bar-btn" wire:click="prevDay" title="{{ __('app.prev_day') }}">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        <div class="date-bar-track">
                            @foreach($dateRange as $d)
                                <button class="date-cell {{ $d['isSelected'] ? 'selected' : '' }} {{ $d['isToday'] ? 'is-today' : '' }}" wire:click="goToDate('{{ $d['date'] }}')">
                                    <span class="date-cell-dow">{{ $d['dow'] }}</span>
                                    <span class="date-cell-day">{{ $d['day'] }}</span>
                                    <span class="date-cell-month">{{ $d['month'] }}</span>
                                    @if($d['isToday'])
                                        <span class="date-cell-label">{{ __('app.today') }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        <div class="date-bar-actions">
                            <button class="date-bar-btn" wire:click="nextDay" title="{{ __('app.next_day') }}">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Status + Competition + Search + Date Picker --}}
                <div class="row g-3 align-items-end">
                    @if($this->filterMode === 'date')
                        <div class="col-md-3">
                            <label class="form-label fw-bold fs-sm mb-1">{{ __('app.pick_date') }}</label>
                            <div class="matches-date-picker">
                                <i class="bi bi-calendar3"></i>
                                <input type="text" class="flatpickr-input form-control" wire:change="goToDate($event.target.value)" value="{{ $this->selectedDate }}" data-alt-format="M j, Y" readonly>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-{{ $this->filterMode === 'date' ? '3' : '4' }}">
                        <label class="form-label fw-bold fs-sm mb-1">{{ __('app.match_status') }}</label>
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="">{{ __('app.all') }}</option>
                            <option value="scheduled">{{ __('app.scheduled') }}</option>
                            <option value="in_progress">{{ __('app.in_progress') }}</option>
                            <option value="completed">{{ __('app.completed') }}</option>
                            <option value="postponed">{{ __('app.postponed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-{{ $this->filterMode === 'date' ? '3' : '4' }}">
                        <label class="form-label fw-bold fs-sm mb-1">{{ __('app.competition') }}</label>
                        <select class="form-select" wire:model.live="competitionId">
                            <option value="">{{ __('app.all_competitions') }}</option>
                            @foreach($competitions as $comp)
                                <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-{{ $this->filterMode === 'date' ? '3' : '4' }}">
                        <label class="form-label fw-bold fs-sm mb-1">{{ __('app.search') }}</label>
                        <div class="matches-search">
                            <i class="bi bi-search"></i>
                            <input type="text" class="matches-search-input" placeholder="{{ __('app.search_teams') }}" wire:model.live.debounce.300ms="search">
                            @if($search)
                                <button class="matches-search-clear" wire:click="$set('search', '')"><i class="bi bi-x"></i></button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Results --}}
        @if($matches->count())
            <div class="row g-3">
                @foreach($matches as $match)
                    <div class="col-md-6 col-lg-4">
                        <div class="match-card" role="button" onclick="window.location='{{ route('matches.live', $match) }}'" wire:key="match-{{ $match->id }}">
                            <div class="match-card-header">
                                <small class="text-chrome-muted">
                                    <i class="bi bi-trophy"></i> {{ $match->competition?->name ?? __('app.competition') }}
                                </small>
                                <x-status-badge domain="match" status="{{ $match->status }}" set="bi" />
                            </div>
                            <div class="match-card-inner">
                                <div class="match-team match-team-home">
                                    <span class="match-team-name">{{ $match->team1?->name ?? 'TBD' }}</span>
                                </div>
                                <div class="match-center">
                                    @if($match->status === 'completed')
                                        <div class="match-score">{{ $match->score_team1 }} - {{ $match->score_team2 }}</div>
                                        <div class="match-status completed">{{ __('app.full_time') }}</div>
                                    @elseif($match->status === 'in_progress')
                                        <div class="match-score">{{ $match->score_team1 }} - {{ $match->score_team2 }}</div>
                                        <div class="match-status live">
                                            <span class="live-pulse d-inline-block rounded-circle bg-danger me-1" style="width:6px;height:6px;"></span>
                                            {{ __('app.in_progress') }}
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
                            <div class="match-card-footer">
                                <small class="text-chrome-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $match->match_date ? $match->match_date->format('d/m/Y') : '—' }}
                                </small>
                                @if($match->venue)
                                    <small class="text-chrome-muted">
                                        <i class="bi bi-geo-alt me-1"></i> {{ Str::limit($match->venue, 25) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $matches->links() }}
            </div>
        @else
            <div class="empty-state">
                @if($this->filterMode === 'live')
                    <i class="bi bi-broadcast d-block"></i>
                    <h4>{{ __('app.no_live_matches') }}</h4>
                    <p>{{ __('app.no_live_matches_desc') }}</p>
                @else
                    <i class="bi bi-calendar-x d-block"></i>
                    <h4>{{ __('app.no_matches_for_date') }}</h4>
                    <p>{{ __('app.try_another_date') }}</p>
                @endif
            </div>
        @endif
    </div>
</div>
