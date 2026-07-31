<div x-data="lineupInteractions()">
    <style>
        .lineup-pitch { position: relative; background: linear-gradient(180deg, #1a5c2e 0%, #1e6b34 50%, #1a5c2e 100%); border-radius: 12px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .lineup-pitch::before { content: ''; position: absolute; inset: 0; background: repeating-linear-gradient(90deg, transparent, transparent 48px, rgba(255,255,255,0.03) 48px, rgba(255,255,255,0.03) 96px); pointer-events: none; }
        .pitch-lines { position: absolute; inset: 0; pointer-events: none; }
        .player-dot { position: absolute; transform: translate(-50%, -50%); cursor: pointer; z-index: 2; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .player-dot:hover { transform: translate(-50%, -50%) scale(1.15); z-index: 10; }
        .player-dot .jersey { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; color: #fff; border: 3px solid rgba(255,255,255,0.9); box-shadow: 0 4px 12px rgba(0,0,0,0.4), inset 0 -2px 4px rgba(0,0,0,0.2); position: relative; }
        .player-dot .jersey.captain { border-color: #ffc107; box-shadow: 0 4px 12px rgba(0,0,0,0.4), 0 0 0 2px #ffc107; }
        .player-dot .jersey .captain-badge { position: absolute; top: -6px; right: -6px; width: 18px; height: 18px; background: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900; color: #1a1a2e; border: 2px solid #fff; }
        .player-dot .player-name { position: absolute; top: calc(100% + 4px); left: 50%; transform: translateX(-50%); white-space: nowrap; font-size: 11px; font-weight: 700; color: #fff; text-shadow: 0 1px 4px rgba(0,0,0,0.8), 0 0 8px rgba(0,0,0,0.5); text-align: center; line-height: 1.2; }
        .player-dot .player-name .pos-label { display: block; font-size: 9px; font-weight: 500; opacity: 0.7; }
        .team1-jersey { background: linear-gradient(135deg, #1a237e, #283593); }
        .team2-jersey { background: linear-gradient(135deg, #b71c1c, #c62828); }
        .formation-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; }
        .formation-badge:hover { transform: scale(1.05); }
        .bench-player { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); transition: all 0.2s; }
        .bench-player:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.15); }
        .bench-jersey { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; color: #fff; flex-shrink: 0; }
        .stat-pill { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .match-header-card { background: linear-gradient(135deg, rgba(26,30,46,0.95), rgba(20,24,40,0.98)); border: 1px solid rgba(255,193,7,0.15); }
        .team-tab { padding: 10px 20px; border-radius: 10px; cursor: pointer; font-weight: 700; transition: all 0.2s; border: 2px solid transparent; }
        .team-tab.active { border-color: var(--primary); background: rgba(255,193,7,0.1); }
        .team-tab:hover:not(.active) { background: rgba(255,255,255,0.05); }
        .action-btn-row { display: flex; gap: 4px; opacity: 0; transition: opacity 0.2s; }
        .player-dot:hover .action-btn-row { opacity: 1; }
        .lineup-actions-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; }
        .lineup-list-item { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 8px; transition: background 0.15s; }
        .lineup-list-item:hover { background: rgba(255,255,255,0.05); }
        .position-slot-overlay { position: absolute; width: 48px; height: 48px; transform: translate(-50%, -50%); cursor: pointer; z-index: 5; border-radius: 50%; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
        .position-slot-overlay.empty { border: 2px dashed rgba(255,255,255,0.3); background: rgba(255,255,255,0.08); }
        .position-slot-overlay.empty:hover { border-color: #ffc107; background: rgba(255,193,7,0.15); transform: translate(-50%, -50%) scale(1.15); }
        .position-slot-overlay.filled { pointer-events: none; }
        .position-slot-overlay .slot-label { font-size: 9px; font-weight: 600; color: rgba(255,255,255,0.5); text-align: center; line-height: 1.1; }
        .player-picker { position: absolute; z-index: 50; background: rgba(20,24,40,0.98); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 8px; min-width: 200px; max-height: 300px; overflow-y: auto; backdrop-filter: blur(12px); box-shadow: 0 8px 32px rgba(0,0,0,0.6); }
        .player-picker-item { display: flex; align-items: center; gap: 8px; padding: 6px 10px; border-radius: 8px; cursor: pointer; transition: background 0.15s; }
        .player-picker-item:hover { background: rgba(255,255,255,0.08); }
        .player-picker-item .picker-jersey { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 11px; color: #fff; flex-shrink: 0; }
        .avail-player-card { display: flex; align-items: center; gap: 8px; padding: 6px 10px; border-radius: 8px; cursor: grab; transition: all 0.15s; border: 1px solid transparent; }
        .avail-player-card:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.1); }
        .avail-player-card:active { cursor: grabbing; }
        .avail-player-card.dragging { opacity: 0.4; }
        @media (max-width: 768px) {
            .player-dot .jersey { width: 36px; height: 36px; font-size: 13px; }
            .player-dot .player-name { font-size: 9px; }
        }
    </style>
    @vite(['resources/js/lineup.js'])

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.index') }}" class="breadcrumb-link">{{ __('app.matches') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.edit', $match) }}" class="breadcrumb-link">{{ $match->team1->name ?? '?' }} vs {{ $match->team2->name ?? '?' }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.lineup') }}</li>
        </ol>
    </nav>

    {{-- Match Header --}}
    <div class="match-header-card rounded-4 p-4 mb-4 text-center">
        <div class="d-flex align-items-center justify-content-center gap-4 flex-wrap">
            <div class="text-center min-w-140">
                <div class="fw-bold text-white fs-5">{{ $match->team1->name ?? '?' }}</div>
                <div class="mt-2">
                    @foreach($team1Lineup->filter(fn($l) => $l->is_starter) as $s)
                        <span class="stat-pill text-white team1-jersey mb-1">{{ $s->jersey_number ?? '?' }}</span>
                    @endforeach
                </div>
            </div>
            <div class="text-center text-white px-4">
                <div class="text-gold fw-bold fs-1 lh-1">VS</div>
                @if($match->match_date)
                    <div class="mt-1 fs-08">{{ formatDateTime($match->match_date) }}</div>
                @endif
            </div>
            <div class="text-center min-w-140">
                <div class="fw-bold text-white fs-5">{{ $match->team2->name ?? '?' }}</div>
                <div class="mt-2">
                    @foreach($team2Lineup->filter(fn($l) => $l->is_starter) as $s)
                        <span class="stat-pill team2-jersey mb-1">{{ $s->jersey_number ?? '?' }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Team Tabs --}}
    <div class="d-flex justify-content-center gap-3 mb-4">
        <div class="team-tab text-center min-w-180 {{ $activeTeam === 1 ? 'active' : '' }}" wire:click="switchTeamAndOpen(1)">
            <i class="bi bi-shield-fill" style="color:#3949ab;"></i>
            <span class=" fw-bold ms-1">{{ $match->team1->name ?? __('app.team1_name') }}</span>
            <div class=" mt-1 fs-sm">{{ $team1Lineup->filter(fn($l) => $l->is_starter)->count() }} {{ __('app.starters') }} + {{ $team1Lineup->filter(fn($l) => !$l->is_starter)->count() }} {{ __('app.substitutes') }}</div>
        </div>
        <div class="team-tab text-center min-w-180 {{ $activeTeam === 2 ? 'active' : '' }}" wire:click="switchTeamAndOpen(2)">
            <i class="bi bi-shield-fill" style="color:#e53935;"></i>
            <span class="fw-bold ms-1">{{ $match->team2->name ?? __('app.team2_name') }}</span>
            <div class="mt-1 fs-sm">{{ $team2Lineup->filter(fn($l) => $l->is_starter)->count() }} {{ __('app.starters') }} + {{ $team2Lineup->filter(fn($l) => !$l->is_starter)->count() }} {{ __('app.substitutes') }}</div>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="row g-4">
        {{-- Player Sidebar --}}
        <div class="col-lg-3 order-lg-1">
            <div class="lineup-actions-card p-3 mb-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-people-fill text-gold"></i>
                    <h6 class="fw-bold  mb-0 fs-sm">{{ __('app.available_players') }}</h6>
                    <span class="badge bg-warning text-dark ms-auto">{{ $availablePlayers->count() }}</span>
                </div>
                <div style="max-height:400px;overflow-y:auto;">
                    @forelse($availablePlayers as $player)
                        <div class="avail-player-card"
                             draggable="true"
                             @dragstart="onDragStart($event, {{ $player->id }})"
                             wire:key="avail-{{ $player->id }}">
                            <span class="{{ $activeTeam === 1 ? 'team1-jersey' : 'team2-jersey' }} picker-jersey">{{ $player->number ?? '?' }}</span>
                            <div class="flex-grow-1 min-w-0">
                                <div class=" fw-bold text-truncate fs-sm">{{ $player->name }}</div>
                                <small class="fs-2xs">{{ $player->position->abbreviation ?? $player->position_text ?? '' }}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-warning btn-xs rounded-circle p-1"
                                    wire:click="switchTeamAndOpen({{ $activeTeam }})"
                                    wire:key="quick-{{ $player->id }}"
                                    title="{{ __('app.add_to_lineup') }}">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <small class="-50"><i class="bi bi-inbox"></i> {{ __('app.no_available_players') }}</small>
                        </div>
                    @endforelse
                </div>
                <hr class="hr-dark my-2">
                <button class="btn btn-sm btn-warning w-100" wire:click="switchTeamAndOpen({{ $activeTeam }})">
                    <i class="bi bi-plus-lg"></i> {{ __('app.add_player_modal') }}
                </button>
            </div>

            {{-- Quick Stats --}}
            <div class="lineup-actions-card p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-info-circle text-gold"></i>
                    <h6 class="fw-bold  mb-0 fs-sm">{{ __('app.quick_stats') }}</h6>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <small class="-50">{{ __('app.formation') }}</small>
                    <span class=" fw-bold">{{ $activeTeam === 1 ? $selectedFormation1 : $selectedFormation2 }}</span>
                </div>
                @php
                    $currentTeamLineup = $activeTeam === 1 ? $team1Lineup : $team2Lineup;
                    $startersCount = $currentTeamLineup->filter(fn($l) => $l->is_starter)->count();
                    $subsCount = $currentTeamLineup->filter(fn($l) => !$l->is_starter)->count();
                @endphp
                <div class="d-flex justify-content-between mb-1">
                    <small class="-50">{{ __('app.starters') }}</small>
                    <span class="text-warning fw-bold">{{ $startersCount }}/11</span>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="-50">{{ __('app.substitutes') }}</small>
                    <span class="fw-bold">{{ $subsCount }}</span>
                </div>
            </div>
        </div>

        {{-- Pitch Display --}}
        <div class="col-lg-6 order-lg-2">
            <div class="card border-0 shadow-sm card-dark">
                <div class="card-body p-3">
                    {{-- Formation Selector --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-base">{{ __('app.formation') }}:</span>
                            <div class="dropdown">
                                <button class="formation-badge team{{ $activeTeam }}-jersey  dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    {{ $activeTeam === 1 ? $selectedFormation1 : $selectedFormation2 }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark" style="max-height:320px;overflow-y:auto;">
                                    @if($activeTeamFormations->isNotEmpty())
                                        <li class="dropdown-header text-gold fs-2xs">{{ __('app.saved_formations') }}</li>
                                        @foreach($activeTeamFormations as $fmt)
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 {{ $fmt->formation_code === ($activeTeam === 1 ? $selectedFormation1 : $selectedFormation2) ? 'active' : '' }}"
                                                   wire:click="selectFormation({{ $activeTeam }}, '{{ $fmt->formation_code }}')">
                                                    <span class="fw-bold">{{ $fmt->name }}</span>
                                                    <span class="badge bg-primary-subtle text-primary fs-2xs">{{ $fmt->formation_code }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                        <li><hr class="dropdown-divider"></li>
                                    @endif
                                    <li class="dropdown-header fs-2xs">{{ __('app.standard_formations') }}</li>
                                    @foreach($formationsList as $code)
                                        <li><a class="dropdown-item {{ ($activeTeam === 1 ? $selectedFormation1 : $selectedFormation2) === $code ? 'active' : '' }}"
                                               wire:click="selectFormation({{ $activeTeam }}, '{{ $code }}')">{{ $code }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            @if($activeTeamFormations->isNotEmpty())
                                <a href="{{ route('admin.teams.formations', $activeTeam === 1 ? $match->team1 : $match->team2) }}" class="btn btn-sm btn-outline-light rounded-md" title="{{ __('app.manage_formations') }}" target="_blank">
                                    <i class="bi bi-gear"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- SVG Pitch with Interactive Overlay --}}
                    @php
                        $pitchData = $activeTeam === 1 ? $pitchData1 : $pitchData2;
                        $pitchSlots = $activeTeam === 1 ? $pitchSlots1 : $pitchSlots2;
                        $jerseyClass = $activeTeam === 1 ? 'team1-jersey' : 'team2-jersey';
                    @endphp
                    <div class="lineup-pitch pitch-wrapper" style="aspect-ratio: 400/600;" @click.outside="closePicker">
                        @include('livewire.admin.football-pitch', [
                            'positions' => $pitchData,
                            'width' => 400,
                            'height' => 600,
                            'sportType' => 'football',
                            'jerseyClass' => $jerseyClass,
                        ])

                        {{-- Interactive Position Slots Overlay --}}
                        @foreach($pitchSlots as $slot)
                            @php
                                $leftPct = $slot['x'];
                                $topPct = $slot['y'];
                            @endphp
                            <div class="position-slot-overlay {{ $slot['assigned'] ? 'filled' : 'empty' }}"
                                 style="left:{{ $leftPct }}%;top:{{ $topPct }}%;"
                                 @if(!$slot['assigned'])
                                     @click.stop="openPlayerPicker({{ $slot['slot_index'] }})"
                                     @dragover.prevent
                                     @drop.stop="onDrop($event, {{ $slot['slot_index'] }})"
                                 @endif
                                 wire:key="slot-{{ $activeTeam }}-{{ $slot['slot_index'] }}">
                                @if(!$slot['assigned'])
                                    <span class="slot-label">{{ $slot['position'] }}</span>
                                @endif
                            </div>

                            {{-- Player Picker Popover --}}
                            <div x-show="openPicker === {{ $slot['slot_index'] }}"
                                 x-cloak
                                 @click.outside="closePicker"
                                 class="player-picker"
                                 style="left:{{ $leftPct }}%;top:calc({{ $topPct }}% + 26px);transform:translateX(-50%);">
                                <div class="fs-2xs mb-1 px-1">{{ __('app.assign_player') }} - {{ $slot['position'] }}</div>
                                @forelse($availablePlayers as $player)
                                    <div class="player-picker-item"
                                         wire:click="assignToPosition({{ $player->id }}, {{ $slot['slot_index'] }})"
                                         @click="closePicker">
                                        <span class="{{ $jerseyClass }} picker-jersey">{{ $player->number ?? '?' }}</span>
                                        <span class=" fw-bold fs-sm">{{ $player->name }}</span>
                                        <small class="ms-auto">{{ $player->position->abbreviation ?? '' }}</small>
                                    </div>
                                @empty
                                    <div class="text-center py-2">
                                        <small class="-50">{{ __('app.no_available_players') }}</small>
                                    </div>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Lineup Lists --}}
        <div class="col-lg-3 order-lg-3">
            {{-- Team 1 --}}
            <div class="lineup-actions-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold  mb-0 fs-sm">
                        <i class="bi bi-shield-fill" style="color:#3949ab;"></i> {{ $match->team1->name ?? __('app.team1_name') }}
                    </h6>
                    <button class="btn btn-sm btn-outline-warning rounded-md" wire:click="switchTeamAndOpen(1)" title="{{ __('app.add_player') }}">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>

                {{-- Starters --}}
                <div class="mb-2">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-warning text-dark fs-2xs">{{ __('app.starters') }}</span>
                        <span class="fs-2xs">{{ $team1Lineup->filter(fn($l) => $l->is_starter)->count() }}</span>
                    </div>
                    @forelse($team1Lineup->filter(fn($l) => $l->is_starter) as $lineup)
                        <div class="lineup-list-item py-1" wire:key="t1s-{{ $lineup->id }}">
                            <div class="d-flex align-items-center gap-1">
                                <span class="team1-jersey" style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0;">{{ $lineup->jersey_number ?? '-' }}</span>
                                <div class="min-w-0">
                                    <div class=" fw-bold text-truncate" style="font-size:11px;line-height:1.2;">
                                        {{ $lineup->player->name ?? '-' }}
                                        @if($lineup->is_captain)<span class="text-warning fs-2xs">★</span>@endif
                                    </div>
                                    <small class="d-block" style="font-size:9px;line-height:1.5;">{{ $lineup->position->abbreviation ?? '' }}</small>
                                </div>
                            </div>
                            <div class="d-flex gap-1" style="flex-shrink:0;">
                                <button class="btn btn-sm p-0 -50" wire:click="editLineup({{ $lineup->id }})" style="font-size:10px;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 text-danger" wire:click="deleteLineup({{ $lineup->id }})" wire:confirm="{{ __('app.confirm_delete') }}" style="font-size:10px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-1">
                            <small class="fs-2xs"><i class="bi bi-inbox"></i> {{ __('app.no_players') }}</small>
                        </div>
                    @endforelse
                </div>

                {{-- Substitutes --}}
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-secondary fs-2xs">{{ __('app.substitutes') }}</span>
                        <span class="fs-2xs">{{ $team1Lineup->filter(fn($l) => !$l->is_starter)->count() }}</span>
                    </div>
                    @forelse($team1Lineup->filter(fn($l) => !$l->is_starter) as $lineup)
                        <div class="lineup-list-item py-1" wire:key="t1b-{{ $lineup->id }}">
                            <div class="d-flex align-items-center gap-1">
                                <span class="bg-secondary" style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0;">{{ $lineup->jersey_number ?? '-' }}</span>
                                <div class="min-w-0">
                                    <div class=" text-truncate fw-bold" style="font-size:11px;line-height:1.2;">{{ $lineup->player->name ?? '-' }}</div>
                                    @if($lineup->minute_in)
                                        <small class="text-success d-block" style="font-size:8px;line-height:1;">↔ {{ $lineup->minute_in }}'</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-1" style="flex-shrink:0;">
                                <button class="btn btn-sm p-0 -50" wire:click="editLineup({{ $lineup->id }})" style="font-size:10px;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 text-danger" wire:click="deleteLineup({{ $lineup->id }})" wire:confirm="{{ __('app.confirm_delete') }}" style="font-size:10px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-1">
                            <small class="fs-2xs"><i class="bi bi-inbox"></i> {{ __('app.no_substitutes') }}</small>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Team 2 --}}
            <div class="lineup-actions-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold  mb-0 fs-sm">
                        <i class="bi bi-shield-fill" style="color:#e53935;"></i> {{ $match->team2->name ?? __('app.team2_name') }}
                    </h6>
                    <button class="btn btn-sm btn-outline-warning rounded-md" wire:click="switchTeamAndOpen(2)" title="{{ __('app.add_player') }}">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>

                {{-- Starters --}}
                <div class="mb-2">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-warning text-dark fs-2xs">{{ __('app.starters') }}</span>
                        <span class="fs-2xs">{{ $team2Lineup->filter(fn($l) => $l->is_starter)->count() }}</span>
                    </div>
                    @forelse($team2Lineup->filter(fn($l) => $l->is_starter) as $lineup)
                        <div class="lineup-list-item py-1" wire:key="t2s-{{ $lineup->id }}">
                            <div class="d-flex align-items-center gap-1">
                                <span class="team2-jersey" style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0;">{{ $lineup->jersey_number ?? '-' }}</span>
                                <div class="min-w-0">
                                    <div class=" fw-bold text-truncate" style="font-size:11px;line-height:1.2;">
                                        {{ $lineup->player->name ?? '-' }}
                                        @if($lineup->is_captain)<span class="text-warning fs-2xs">★</span>@endif
                                    </div>
                                    <small class="d-block" style="font-size:9px;line-height:1;">{{ $lineup->position->abbreviation ?? '' }}</small>
                                </div>
                            </div>
                            <div class="d-flex gap-1" style="flex-shrink:0;">
                                <button class="btn btn-sm p-0 -50" wire:click="editLineup({{ $lineup->id }})" style="font-size:10px;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 text-danger" wire:click="deleteLineup({{ $lineup->id }})" wire:confirm="{{ __('app.confirm_delete') }}" style="font-size:10px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-1">
                            <small class="fs-2xs"><i class="bi bi-inbox"></i> {{ __('app.no_players') }}</small>
                        </div>
                    @endforelse
                </div>

                {{-- Substitutes --}}
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-secondary fs-2xs">{{ __('app.substitutes') }}</span>
                        <span class="fs-2xs">{{ $team2Lineup->filter(fn($l) => !$l->is_starter)->count() }}</span>
                    </div>
                    @forelse($team2Lineup->filter(fn($l) => !$l->is_starter) as $lineup)
                        <div class="lineup-list-item py-1" wire:key="t2b-{{ $lineup->id }}">
                            <div class="d-flex align-items-center gap-1">
                                <span class="bg-secondary" style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0;">{{ $lineup->jersey_number ?? '-' }}</span>
                                <div class="min-w-0">
                                    <div class=" text-truncate fw-bold" style="font-size:11px;line-height:1.2;">{{ $lineup->player->name ?? '-' }}</div>
                                    @if($lineup->minute_in)
                                        <small class="text-success d-block" style="font-size:8px;line-height:1;">↔ {{ $lineup->minute_in }}'</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-1" style="flex-shrink:0;">
                                <button class="btn btn-sm p-0 -50" wire:click="editLineup({{ $lineup->id }})" style="font-size:10px;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 text-danger" wire:click="deleteLineup({{ $lineup->id }})" wire:confirm="{{ __('app.confirm_delete') }}" style="font-size:10px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-1">
                            <small class="fs-2xs"><i class="bi bi-inbox"></i> {{ __('app.no_substitutes') }}</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($showModal)
        <div class="modal d-block modal-overlay-blur" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="lineupModalTitle" wire:click.self="closeModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content modal-dark" @click.away="closeModal">
                    <div class="modal-header border-bottom border-modal">
                        <h5 class="modal-title fw-bold " id="lineupModalTitle">
                            <i class="bi bi-people-fill text-gold"></i>
                            {{ $editingLineupId ? __('app.edit_player') : __('app.add_player') }}
                            <span class="text-gold mx-1">-</span>
                            {{ $activeTeam === 1 ? ($match->team1->name ?? '') : ($match->team2->name ?? '') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger d-flex align-items-center gap-2 error-alert-dark">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold -50">{{ __('app.player') }}</label>
                                <select class="form-select form-dark" wire:model="lineupForm.player_id">
                                    <option value="">-- {{ __('app.choose_player_lineup') }} --</option>
                                    @foreach($players as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->number ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold -50">{{ __('app.position') }}</label>
                                <select class="form-select form-dark" wire:model="lineupForm.position_id">
                                    <option value="">-- {{ __('app.choose_position_lineup') }} --</option>
                                    @php
                                        $grouped = $positions->groupBy('category');
                                        $cats = ['goalkeeper' => __('app.goalkeeper'), 'defender' => __('app.defender'), 'midfielder' => __('app.midfielder'), 'forward' => __('app.forward')];
                                    @endphp
                                    @foreach($cats as $catKey => $catLabel)
                                        @if($grouped->has($catKey))
                                            <optgroup label="{{ $catLabel }}">
                                                @foreach($grouped[$catKey] as $pos)
                                                    <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->abbreviation }})</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold -50">{{ __('app.jersey_number') }}</label>
                                <input type="number" class="form-control form-dark" wire:model="lineupForm.jersey_number" min="0" max="99">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold -50">{{ __('app.is_starter') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="lineupForm.is_starter" id="isStarterCheck">
                                    <label class="form-check-label fw-bold " for="isStarterCheck">
                                        {{ $lineupForm['is_starter'] ? __('app.is_starter') : __('app.is_bench') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold -50">{{ __('app.is_captain_label') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="lineupForm.is_captain" id="isCaptainCheck">
                                    <label class="form-check-label fw-bold " for="isCaptainCheck">
                                        {{ $lineupForm['is_captain'] ? __('app.is_captain_label') : __('app.is_normal') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-sm btn-outline-info w-100 rounded-md" wire:click="$set('lineupForm.is_starter', true); $set('lineupForm.is_captain', false);">
                                    <i class="bi bi-star"></i> {{ __('app.add_as_starter') }}
                                </button>
                            </div>

                            @if(!$lineupForm['is_starter'])
                                <div class="col-12"><hr class="hr-dark"></div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold -50">{{ __('app.minute_in') }}</label>
                                    <input type="number" class="form-control form-dark" wire:model="lineupForm.minute_in" min="0" max="120">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold -50">{{ __('app.minute_out') }}</label>
                                    <input type="number" class="form-control form-dark" wire:model="lineupForm.minute_out" min="0" max="120">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold -50">{{ __('app.sub_reason') }}</label>
                                    <select class="form-select form-dark" wire:model="lineupForm.sub_reason">
                                        <option value="">-- {{ __('app.no_substitutes') }} --</option>
                                        @foreach($subReasons as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-12">
                                <label class="form-label fw-bold -50">{{ __('app.performance_notes') }}</label>
                                <textarea class="form-control form-dark" wire:model="lineupForm.performance_notes" rows="2" placeholder="{{ __('app.performance_notes_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-modal">
                        <button type="button" class="btn btn-secondary rounded-md" wire:click="closeModal">{{ __('app.cancel') }}</button>
                        <button type="button" class="btn btn-warning px-4 rounded-md" wire:click="saveLineup" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveLineup"><i class="bi bi-check-lg"></i> {{ __('app.save') }}</span>
                            <span wire:loading wire:target="saveLineup"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
