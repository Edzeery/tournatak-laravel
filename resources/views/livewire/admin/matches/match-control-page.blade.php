<div>
    <x-section-header
        icon="bi bi-controller"
        :title="__('app.match_control')"
    >
        <x-slot:subtitle>
            {{ $match->competition->name ?? '' }}
            @if($match->round) · {{ $match->round }} @endif
        </x-slot:subtitle>
        <x-slot:action>
            <a href="{{ route('admin.matches.index') }}" class="btn btn-outline-secondary rounded-md btn-sm">
                <i class="bi bi-arrow-left"></i> {{ __('app.back') }}
            </a>
        </x-slot:action>
    </x-section-header>

    {{-- Main Scoreboard --}}
    <div class="card border-0 mb-4 overflow-hidden">
        <div class="card-body text-center py-4 position-relative"
            style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">

            {{-- Live badge --}}
            @if($match->phase === 'first_half' || $match->phase === 'second_half' || str_starts_with($match->phase, 'et_'))
                <div class="position-absolute top-0 start-0 mt-3 ms-3">
                    <span class="badge bg-danger live-pulse"><span class="pulse-dot rounded-circle bg-white d-inline-block me-1" style="width:5px;height:5px;"></span> LIVE</span>
                </div>
            @endif
            @if($match->phase === 'full_time')
                <div class="position-absolute top-0 start-0 mt-3 ms-3">
                    <span class="badge bg-secondary">FT</span>
                </div>
            @endif

            {{-- Match timer — uses shared Alpine component from app.js --}}
            <div class="mb-3" wire:ignore
                x-data="matchTimer({
                    phase: '{{ $match->phase }}',
                    fhs: {{ $match->first_half_started_at ? strtotime($match->first_half_started_at) * 1000 : 'null' }},
                    shs: {{ $match->second_half_started_at ? strtotime($match->second_half_started_at) * 1000 : 'null' }},
                    et1s: {{ $match->et_first_half_started_at ? strtotime($match->et_first_half_started_at) * 1000 : 'null' }},
                    et2s: {{ $match->et_second_half_started_at ? strtotime($match->et_second_half_started_at) * 1000 : 'null' }},
                    at1: {{ $match->added_time_first_half ?? 0 }},
                    at2: {{ $match->added_time_second_half ?? 0 }},
                    ate1: {{ $match->extra_data['added_time_et_first_half'] ?? 0 }},
                    ate2: {{ $match->extra_data['added_time_et_second_half'] ?? 0 }},
                    mode: 'full',
                })"
            >
                <div class="fs-4xl fw-bold text-white mb-0" style="font-size:3.5rem;letter-spacing:2px;font-variant-numeric:tabular-nums;">
                    <span x-text="period"></span> <span x-text="display"></span>
                </div>
                <div class="text-chrome-subtle fs-xs mt-1">{{ __('app.phase') }}: {{ $match->phase_label }}</div>
            </div>

            {{-- Teams & Score --}}
            <div class="d-flex align-items-center justify-content-center gap-4 gap-md-5">
                {{-- Team 1 --}}
                <div class="text-center" style="flex:1;">
                    @if($match->team1)
                        <div class="d-flex justify-content-center mb-2">
                            @if($match->team1->logo)
                                <img src="{{ $match->team1->logo_url }}" alt="{{ $match->team1->name }}"
                                    class="rounded-circle object-cover border border-2 border-chrome flex-shrink-0"
                                    style="width:64px;height:64px;">
                            @else
                                <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                    style="width:64px;height:64px;font-size:1.5rem;">
                                    {{ mb_substr($match->team1->name ?? '?', 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h5 class="text-white fw-bold mb-0">{{ $match->team1->name }}</h5>
                    @endif
                </div>

                {{-- Score --}}
                <div class="text-center flex-shrink-0" style="min-width:200px;">
                    {{-- Team 1 score controls --}}
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                        <span class="text-chrome-subtle fs-2xs fw-semibold" style="min-width:60px;text-align:right;">
                            {{ $match->team1->name ?? '' }}
                        </span>
                        <button class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center"
                            style="width:30px;height:30px;" wire:click="scoreDown(1)"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif
                            title="{{ __('app.subtract_goal') }}">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <span class="fw-bold text-white" style="font-size:2.5rem;font-variant-numeric:tabular-nums;min-width:40px;">
                            {{ $score1 }}
                        </span>
                        <button class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center"
                            style="width:30px;height:30px;" wire:click="scoreUp(1)"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif
                            title="{{ __('app.add_goal') }}">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>

                    {{-- Separator --}}
                    <div class="text-chrome-subtle" style="font-size:1.5rem;line-height:1;">:</div>

                    {{-- Team 2 score controls --}}
                    <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                        <span class="text-chrome-subtle fs-2xs fw-semibold" style="min-width:60px;text-align:right;">
                            {{ $match->team2->name ?? '' }}
                        </span>
                        <button class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center"
                            style="width:30px;height:30px;" wire:click="scoreDown(2)"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif
                            title="{{ __('app.subtract_goal') }}">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <span class="fw-bold text-white" style="font-size:2.5rem;font-variant-numeric:tabular-nums;min-width:40px;">
                            {{ $score2 }}
                        </span>
                        <button class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center"
                            style="width:30px;height:30px;" wire:click="scoreUp(2)"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif
                            title="{{ __('app.add_goal') }}">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- Team 2 --}}
                <div class="text-center" style="flex:1;">
                    @if($match->team2)
                        <div class="d-flex justify-content-center mb-2">
                            @if($match->team2->logo)
                                <img src="{{ $match->team2->logo_url }}" alt="{{ $match->team2->name }}"
                                    class="rounded-circle object-cover border border-2 border-chrome flex-shrink-0"
                                    style="width:64px;height:64px;">
                            @else
                                <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                    style="width:64px;height:64px;font-size:1.5rem;">
                                    {{ mb_substr($match->team2->name ?? '?', 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h5 class="text-white fw-bold mb-0">{{ $match->team2->name }}</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Phase Controls --}}
        <div class="col-lg-4">
            <div class="card border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 text-theme-primary section-title-dark">
                        <i class="bi bi-play-circle text-gold"></i> {{ __('app.phase_controls') }}
                    </h6>

                    <div class="d-flex flex-column gap-2">
                        @if($match->phase === 'scheduled')
                            <button class="btn btn-success rounded-md" wire:click="startFirstHalf">
                                <i class="bi bi-play-fill"></i> {{ __('app.start_first_half') }}
                            </button>
                        @endif

                        @if($match->phase === 'first_half')
                            <button class="btn btn-warning rounded-md" wire:click="endFirstHalf">
                                <i class="bi bi-stop-fill"></i> {{ __('app.end_first_half') }}
                            </button>
                        @endif

                        @if($match->phase === 'half_time')
                            <button class="btn btn-success rounded-md" wire:click="startSecondHalf">
                                <i class="bi bi-play-fill"></i> {{ __('app.start_second_half') }}
                            </button>
                        @endif

                        @if($match->phase === 'second_half')
                            <button class="btn btn-warning rounded-md" wire:click="endSecondHalf">
                                <i class="bi bi-stop-fill"></i>
                                @if($supportsET)
                                    {{ __('app.end_second_half') }}
                                @else
                                    {{ __('app.end_match') }}
                                @endif
                            </button>
                        @endif

                        @if($match->phase === 'et_break')
                            <button class="btn btn-success rounded-md" wire:click="startETFirstHalf">
                                <i class="bi bi-play-fill"></i> {{ __('app.start_et_first_half') }}
                            </button>
                        @endif

                        @if($match->phase === 'et_first_half')
                            <button class="btn btn-warning rounded-md" wire:click="endETFirstHalf">
                                <i class="bi bi-stop-fill"></i> {{ __('app.end_et_first_half') }}
                            </button>
                        @endif

                        @if($match->phase === 'et_half_time')
                            <button class="btn btn-success rounded-md" wire:click="startETSecondHalf">
                                <i class="bi bi-play-fill"></i> {{ __('app.start_et_second_half') }}
                            </button>
                        @endif

                        @if($match->phase === 'et_second_half')
                            <button class="btn btn-warning rounded-md" wire:click="endMatch">
                                <i class="bi bi-stop-fill"></i> {{ __('app.end_match') }}
                            </button>
                        @endif

                        @if(in_array($match->phase, [\App\Models\Match_::PHASE_FIRST_HALF, \App\Models\Match_::PHASE_SECOND_HALF, \App\Models\Match_::PHASE_ET_FIRST_HALF, \App\Models\Match_::PHASE_ET_SECOND_HALF]))
                            <hr class="my-2">
                            <button class="btn btn-outline-danger rounded-md btn-sm" wire:click="endMatch"
                                wire:confirm="{{ __('app.confirm_end_match') }}">
                                <i class="bi bi-stop-circle"></i> {{ __('app.force_end_match') }}
                            </button>
                        @endif

                        @if($match->phase === 'et_break')
                            <hr class="my-2">
                            <div class="d-grid">
                                <button class="btn btn-outline-secondary rounded-md btn-sm" wire:click="endMatch"
                                    wire:confirm="{{ __('app.confirm_end_match_no_et') }}">
                                    <i class="bi bi-check-circle"></i> {{ __('app.end_match_no_et') }}
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Current phase badge --}}
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-chrome-muted">{{ __('app.current_phase') }}</small>
                            <x-status-badge domain="match" :status="$match->phase" set="bi" />
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-chrome-muted">{{ __('app.match_status') }}</small>
                            <x-status-badge domain="match" :status="$match->status" set="bi" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Added Time + Quick Events --}}
        <div class="col-lg-4">
            <div class="card border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 text-theme-primary section-title-dark">
                        <i class="bi bi-clock text-gold"></i> {{ __('app.added_time') }}
                    </h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fs-xs text-chrome-muted">{{ __('app.added_time_1st') }}</label>
                            <input type="number" class="form-control form-control-sm" wire:model.live="addedTime1" min="0" max="30">
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-xs text-chrome-muted">{{ __('app.added_time_2nd') }}</label>
                            <input type="number" class="form-control form-control-sm" wire:model.live="addedTime2" min="0" max="30">
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-xs text-chrome-muted">{{ __('app.added_time_et1') }}</label>
                            <input type="number" class="form-control form-control-sm" wire:model.live="addedTimeET1" min="0" max="15">
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-xs text-chrome-muted">{{ __('app.added_time_et2') }}</label>
                            <input type="number" class="form-control form-control-sm" wire:model.live="addedTimeET2" min="0" max="15">
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary rounded-md mt-2 w-100" wire:click="saveAddedTime">
                        <i class="bi bi-save"></i> {{ __('app.save_added_time') }}
                    </button>
                </div>
            </div>

            <div class="card border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 text-theme-primary section-title-dark">
                        <i class="bi bi-lightning text-gold"></i> {{ __('app.quick_events') }}
                    </h6>

                    <div class="row g-2 mb-2">
                        <div class="col-4">
                            <label class="form-label fs-xs text-chrome-muted">{{ __('app.minute') }}</label>
                            <input type="number" class="form-control form-control-sm"
                                placeholder="'" wire:model.live="eventMinute" min="0" max="150">
                        </div>
                        <div class="col-8">
                            <label class="form-label fs-xs text-chrome-muted">{{ __('app.description') }}</label>
                            <input type="text" class="form-control form-control-sm"
                                placeholder="{{ __('app.event_desc_placeholder') }}"
                                wire:model.live="eventDescription">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-xs text-chrome-muted">{{ __('app.player') }}</label>
                        <select class="form-select form-select-sm" wire:model.live="selectedPlayerId">
                            <option value="">{{ __('app.select_player') }}</option>
                            <optgroup label="{{ $match->team1->name ?? '' }}">
                                @foreach($playersByTeam[$match->team1_id] ?? [] as $p)
                                    <option value="{{ $p->id }}">#{{ $p->number ?? '?' }} {{ $p->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="{{ $match->team2->name ?? '' }}">
                                @foreach($playersByTeam[$match->team2_id] ?? [] as $p)
                                    <option value="{{ $p->id }}">#{{ $p->number ?? '?' }} {{ $p->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button class="btn btn-sm btn-outline-success rounded-md"
                            wire:click="quickGoal({{ $match->team1_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            ⚽ {{ __('app.goal') }} 1
                        </button>
                        <button class="btn btn-sm btn-outline-success rounded-md"
                            wire:click="quickGoal({{ $match->team2_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            ⚽ {{ __('app.goal') }} 2
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-md"
                            wire:click="quickOwnGoal({{ $match->team1_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            ⚽⬅ {{ __('app.own_goal') }} 1
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-md"
                            wire:click="quickOwnGoal({{ $match->team2_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            ⚽⬅ {{ __('app.own_goal') }} 2
                        </button>
                        <button class="btn btn-sm btn-outline-info rounded-md"
                            wire:click="quickSubstitution({{ $match->team1_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            🔄 {{ __('app.substitution') }} 1
                        </button>
                        <button class="btn btn-sm btn-outline-info rounded-md"
                            wire:click="quickSubstitution({{ $match->team2_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            🔄 {{ __('app.substitution') }} 2
                        </button>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-sm btn-outline-warning rounded-md"
                            wire:click="quickYellowCard({{ $match->team1_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            🟨 {{ __('app.yellow_card') }} 1
                        </button>
                        <button class="btn btn-sm btn-outline-warning rounded-md"
                            wire:click="quickYellowCard({{ $match->team2_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            🟨 {{ __('app.yellow_card') }} 2
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-md"
                            wire:click="quickRedCard({{ $match->team1_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            🟥 {{ __('app.red_card') }} 1
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-md"
                            wire:click="quickRedCard({{ $match->team2_id }})"
                            @if($match->phase === 'full_time' || $match->phase === 'scheduled') disabled @endif>
                            🟥 {{ __('app.red_card') }} 2
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Event Log --}}
        <div class="col-lg-4">
            <div class="card border-0 h-100">
                <div class="card-body d-flex flex-column">
                    <h6 class="fw-bold mb-3 text-theme-primary section-title-dark">
                        <i class="bi bi-clock-history text-gold"></i> {{ __('app.event_log') }}
                        <span class="badge bg-secondary rounded-pill fs-2xs ms-1">{{ $match->events->count() }}</span>
                    </h6>

                    <div style="max-height:400px;overflow-y:auto;" class="flex-grow-1" wire:loading.class="opacity-50"
                        x-data x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)">
                        @forelse($match->events as $event)
                            <div class="d-flex align-items-center gap-2 py-2 {{ !$loop->last ? 'border-bottom border-chrome-subtle' : '' }}">
                                <span class="fw-bold fs-xs text-chrome-muted flex-shrink-0 font-monospace" style="min-width:36px;">
                                    {{ $event->minute }}'
                                </span>
                                <span class="flex-shrink-0 fs-xs">
                                    @switch($event->event_type)
                                        @case('goal')
                                        @case('penalty_scored')
                                            <span class="text-success">⚽</span>
                                            @break
                                        @case('own_goal')
                                            <span class="text-danger">⚽⬅</span>
                                            @break
                                        @case('yellow_card')
                                            <span class="text-warning">🟨</span>
                                            @break
                                        @case('second_yellow')
                                            <span class="text-warning">🟨🟨</span>
                                            @break
                                        @case('red_card')
                                            <span>🟥</span>
                                            @break
                                        @case('substitution_in')
                                            <span class="text-info">🔄</span>
                                            @break
                                        @case('substitution_out')
                                            <span class="text-info">⬅</span>
                                            @break
                                        @case('penalty_missed')
                                            <span class="text-danger">❌</span>
                                            @break
                                        @case('save')
                                            <span class="text-primary">🧤</span>
                                            @break
                                        @case('assist')
                                            <span class="text-success">🎯</span>
                                            @break
                                        @default
                                            <span>📌</span>
                                    @endswitch
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold fs-sm text-truncate">
                                        @if($event->player)
                                            <span class="text-chrome-muted fw-normal">#{{ $event->player->number ?? '?' }}</span>
                                            {{ $event->player->name }}
                                        @else
                                            <span class="text-chrome-muted fst-italic">{{ __('app.unknown_player') }}</span>
                                        @endif
                                    </div>
                                    <div class="text-chrome-muted fs-xs text-truncate">
                                        <span class="badge bg-chrome-subtle text-chrome-muted fw-normal px-1" style="font-size:0.65rem;">
                                            {{ $event->team->name ?? '' }}
                                        </span>
                                        @if($event->description && $event->description !== $event->player?->name)
                                            {{ $event->description }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-inbox d-block mb-2 fs-2xl text-chrome-muted"></i>
                                <p class="text-chrome-muted fs-sm mb-0">{{ __('app.no_events_yet') }}</p>
                                <p class="text-chrome-muted fs-2xs mt-1">{{ __('app.use_quick_events_or_manage') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-3 pt-2 border-top border-chrome-subtle">
                        <a href="{{ route('admin.matches.events', $match) }}"
                            class="btn btn-sm btn-outline-warning rounded-md w-100">
                            <i class="bi bi-plus-circle"></i> {{ __('app.manage_events') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lineup & Stats quick links --}}
    <div class="d-flex flex-wrap gap-2 mt-3">
        <a href="{{ route('admin.matches.lineup', $match) }}" class="btn btn-sm btn-outline-success rounded-md">
            <i class="bi bi-people-fill"></i> {{ __('app.lineup') }}
        </a>
        <a href="{{ route('admin.matches.stats', $match) }}" class="btn btn-sm btn-outline-info rounded-md">
            <i class="bi bi-bar-chart-line"></i> {{ __('app.match_stats') }}
        </a>
        <a href="{{ route('admin.matches.edit', $match) }}" class="btn btn-sm btn-outline-primary rounded-md">
            <i class="bi bi-pencil"></i> {{ __('app.edit') }}
        </a>
    </div>
</div>
