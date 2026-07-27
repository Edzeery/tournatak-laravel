<div>
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
        @media (max-width: 768px) {
            .player-dot .jersey { width: 36px; height: 36px; font-size: 13px; }
            .player-dot .player-name { font-size: 9px; }
        }
    </style>

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.index') }}" class="text-decoration-none" style="color:var(--primary);">{{ __('app.matches') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.edit', $match) }}" class="text-decoration-none" style="color:var(--primary);">{{ $match->team1->name ?? '?' }} vs {{ $match->team2->name ?? '?' }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.lineup') }}</li>
        </ol>
    </nav>

    {{-- Match Header --}}
    <div class="match-header-card rounded-4 p-4 mb-4 text-center">
        <div class="d-flex align-items-center justify-content-center gap-4 flex-wrap">
            <div class="text-center" style="min-width:140px;">
                <div class="fw-bold text-white fs-5">{{ $match->team1->name ?? '?' }}</div>
                <div class="mt-2">
                    @foreach($team1Lineup->filter(fn($l) => $l->is_starter) as $s)
                        <span class="stat-pill team1-jersey mb-1">{{ $s->jersey_number ?? '?' }}</span>
                    @endforeach
                </div>
            </div>
            <div class="text-center px-4">
                <div class="text-gold fw-bold fs-1" style="line-height:1;">VS</div>
                @if($match->match_date)
                    <div class="text-white-50 mt-1" style="font-size:0.8rem;">{{ formatDateTime($match->match_date) }}</div>
                @endif
            </div>
            <div class="text-center" style="min-width:140px;">
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
        <div class="team-tab text-center {{ $activeTeam === 1 ? 'active' : '' }}" wire:click="switchTeamAndOpen(1)" style="min-width:180px;">
            <i class="bi bi-shield-fill" style="color:#3949ab;"></i>
            <span class="text-white fw-bold ms-1">{{ $match->team1->name ?? __('app.team1_name') }}</span>
            <div class="text-white-50 mt-1" style="font-size:0.75rem;">{{ $team1Lineup->filter(fn($l) => $l->is_starter)->count() }} {{ __('app.starters') }} + {{ $team1Lineup->filter(fn($l) => !$l->is_starter)->count() }} {{ __('app.substitutes') }}</div>
        </div>
        <div class="team-tab text-center {{ $activeTeam === 2 ? 'active' : '' }}" wire:click="switchTeamAndOpen(2)" style="min-width:180px;">
            <i class="bi bi-shield-fill" style="color:#e53935;"></i>
            <span class="text-white fw-bold ms-1">{{ $match->team2->name ?? __('app.team2_name') }}</span>
            <div class="text-white-50 mt-1" style="font-size:0.75rem;">{{ $team2Lineup->filter(fn($l) => $l->is_starter)->count() }} {{ __('app.starters') }} + {{ $team2Lineup->filter(fn($l) => !$l->is_starter)->count() }} {{ __('app.substitutes') }}</div>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="row g-4">
        {{-- Pitch Display --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08) !important;">
                <div class="card-body p-3">
                    {{-- Formation Selector --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-white-50" style="font-size:0.85rem;">{{ __('app.formation') }}:</span>
                            <div class="dropdown">
                                <button class="formation-badge team{{ $activeTeam }}-jersey text-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    {{ $activeTeam === 1 ? $selectedFormation1 : $selectedFormation2 }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark">
                                    @foreach($formationsList as $code)
                                        <li><a class="dropdown-item {{ ($activeTeam === 1 ? $selectedFormation1 : $selectedFormation2) === $code ? 'active' : '' }}"
                                               wire:click="selectFormation({{ $activeTeam }}, '{{ $code }}')">{{ $code }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-warning" wire:click="switchTeamAndOpen({{ $activeTeam }})">
                            <i class="bi bi-plus-lg"></i> {{ __('app.add_player') }}
                        </button>
                    </div>

                    {{-- SVG Pitch --}}
                    @php
                        $pitchData = $activeTeam === 1 ? $pitchData1 : $pitchData2;
                        $jerseyClass = $activeTeam === 1 ? 'team1-jersey' : 'team2-jersey';
                    @endphp
                    <div class="lineup-pitch" style="aspect-ratio: 2/3; max-height: 520px;">
                        @include('livewire.admin.football-pitch', [
                            'positions' => $pitchData,
                            'width' => 400,
                            'height' => 600,
                            'sportType' => 'football',
                            'jerseyClass' => $jerseyClass,
                        ])
                    </div>
                </div>
            </div>
        </div>

        {{-- Lineup Lists --}}
        <div class="col-lg-4">
            {{-- Team 1 --}}
            <div class="lineup-actions-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-white mb-0">
                        <i class="bi bi-shield-fill" style="color:#3949ab;"></i> {{ $match->team1->name ?? __('app.team1_name') }}
                    </h6>
                    <button class="btn btn-sm btn-outline-warning" wire:click="switchTeamAndOpen(1)" style="border-radius:8px;">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>

                {{-- Starters --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark" style="font-size:0.7rem;">{{ __('app.starters') }}</span>
                        <span class="text-white-50" style="font-size:0.75rem;">{{ $team1Lineup->filter(fn($l) => $l->is_starter)->count() }}</span>
                    </div>
                    @forelse($team1Lineup->filter(fn($l) => $l->is_starter) as $lineup)
                        <div class="lineup-list-item" wire:key="t1s-{{ $lineup->id }}">
                            <div class="d-flex align-items-center gap-2">
                                <span class="team1-jersey bench-jersey">{{ $lineup->jersey_number ?? '-' }}</span>
                                <div>
                                    <div class="text-white fw-bold" style="font-size:0.85rem;">
                                        {{ $lineup->player->name ?? '-' }}
                                        @if($lineup->is_captain)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;">C</span>
                                        @endif
                                    </div>
                                    <small class="text-white-50" style="font-size:0.7rem;">{{ $lineup->position->name ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-light" style="border-radius:6px;padding:2px 6px;font-size:0.7rem;" wire:click="editLineup({{ $lineup->id }})"
                                    aria-label="{{ __('app.edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" style="border-radius:6px;padding:2px 6px;font-size:0.7rem;"
                                        wire:click="deleteLineup({{ $lineup->id }})"
                                        wire:confirm="{{ __('app.confirm_delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-2">
                            <small class="text-white-50"><i class="bi bi-inbox"></i> {{ __('app.no_players') }}</small>
                        </div>
                    @endforelse
                </div>

                {{-- Substitutes --}}
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-secondary" style="font-size:0.7rem;">{{ __('app.substitutes') }}</span>
                        <span class="text-white-50" style="font-size:0.75rem;">{{ $team1Lineup->filter(fn($l) => !$l->is_starter)->count() }}</span>
                    </div>
                    @forelse($team1Lineup->filter(fn($l) => !$l->is_starter) as $lineup)
                        <div class="lineup-list-item" wire:key="t1b-{{ $lineup->id }}">
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg-secondary bench-jersey">{{ $lineup->jersey_number ?? '-' }}</span>
                                <div>
                                    <div class="text-white fw-bold" style="font-size:0.85rem;">{{ $lineup->player->name ?? '-' }}</div>
                                    <small class="text-white-50" style="font-size:0.7rem;">{{ $lineup->position->name ?? '-' }}</small>
                                    @if($lineup->minute_in)
                                        <small class="text-success ms-1" style="font-size:0.65rem;">↔ {{ $lineup->minute_in }}'</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-light" style="border-radius:6px;padding:2px 6px;font-size:0.7rem;" wire:click="editLineup({{ $lineup->id }})"
                                    aria-label="{{ __('app.edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" style="border-radius:6px;padding:2px 6px;font-size:0.7rem;"
                                        wire:click="deleteLineup({{ $lineup->id }})"
                                        wire:confirm="{{ __('app.confirm_delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-2">
                            <small class="text-white-50"><i class="bi bi-inbox"></i> {{ __('app.no_substitutes') }}</small>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Team 2 --}}
            <div class="lineup-actions-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-white mb-0">
                        <i class="bi bi-shield-fill" style="color:#e53935;"></i> {{ $match->team2->name ?? __('app.team2_name') }}
                    </h6>
                    <button class="btn btn-sm btn-outline-warning" wire:click="switchTeamAndOpen(2)" style="border-radius:8px;">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>

                {{-- Starters --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark" style="font-size:0.7rem;">{{ __('app.starters') }}</span>
                        <span class="text-white-50" style="font-size:0.75rem;">{{ $team2Lineup->filter(fn($l) => $l->is_starter)->count() }}</span>
                    </div>
                    @forelse($team2Lineup->filter(fn($l) => $l->is_starter) as $lineup)
                        <div class="lineup-list-item" wire:key="t2s-{{ $lineup->id }}">
                            <div class="d-flex align-items-center gap-2">
                                <span class="team2-jersey bench-jersey">{{ $lineup->jersey_number ?? '-' }}</span>
                                <div>
                                    <div class="text-white fw-bold" style="font-size:0.85rem;">
                                        {{ $lineup->player->name ?? '-' }}
                                        @if($lineup->is_captain)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;">C</span>
                                        @endif
                                    </div>
                                    <small class="text-white-50" style="font-size:0.7rem;">{{ $lineup->position->name ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-light" style="border-radius:6px;padding:2px 6px;font-size:0.7rem;" wire:click="editLineup({{ $lineup->id }})"
                                    aria-label="{{ __('app.edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" style="border-radius:6px;padding:2px 6px;font-size:0.7rem;"
                                        wire:click="deleteLineup({{ $lineup->id }})"
                                        wire:confirm="{{ __('app.confirm_delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-2">
                            <small class="text-white-50"><i class="bi bi-inbox"></i> {{ __('app.no_players') }}</small>
                        </div>
                    @endforelse
                </div>

                {{-- Substitutes --}}
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-secondary" style="font-size:0.7rem;">{{ __('app.substitutes') }}</span>
                        <span class="text-white-50" style="font-size:0.75rem;">{{ $team2Lineup->filter(fn($l) => !$l->is_starter)->count() }}</span>
                    </div>
                    @forelse($team2Lineup->filter(fn($l) => !$l->is_starter) as $lineup)
                        <div class="lineup-list-item" wire:key="t2b-{{ $lineup->id }}">
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg-secondary bench-jersey">{{ $lineup->jersey_number ?? '-' }}</span>
                                <div>
                                    <div class="text-white fw-bold" style="font-size:0.85rem;">{{ $lineup->player->name ?? '-' }}</div>
                                    <small class="text-white-50" style="font-size:0.7rem;">{{ $lineup->position->name ?? '-' }}</small>
                                    @if($lineup->minute_in)
                                        <small class="text-success ms-1" style="font-size:0.65rem;">↔ {{ $lineup->minute_in }}'</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-light" style="border-radius:6px;padding:2px 6px;font-size:0.7rem;" wire:click="editLineup({{ $lineup->id }})"
                                    aria-label="{{ __('app.edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" style="border-radius:6px;padding:2px 6px;font-size:0.7rem;"
                                        wire:click="deleteLineup({{ $lineup->id }})"
                                        wire:confirm="{{ __('app.confirm_delete') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-2">
                            <small class="text-white-50"><i class="bi bi-inbox"></i> {{ __('app.no_substitutes') }}</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($showModal)
        <div class="modal d-block" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="lineupModalTitle" style="background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);" wire:click.self="closeModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="background:#1a1e2e; border:1px solid rgba(255,193,7,0.2); border-radius:16px;" @click.away="closeModal">
                    <div class="modal-header border-bottom" style="border-color:rgba(255,255,255,0.1) !important;">
                        <h5 class="modal-title fw-bold text-white" id="lineupModalTitle">
                            <i class="bi bi-people-fill text-gold"></i>
                            {{ $editingLineupId ? __('app.edit_player') : __('app.add_player') }}
                            <span class="text-gold mx-1">-</span>
                            {{ $activeTeam === 1 ? ($match->team1->name ?? '') : ($match->team2->name ?? '') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger d-flex align-items-center gap-2" style="background:rgba(229,57,53,0.15); border-color:rgba(229,57,53,0.3); color:#ef9a9a;">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-white-50">{{ __('app.player') }}</label>
                                <select class="form-select" style="background:#0d1117; color:#fff; border-color:rgba(255,255,255,0.1);" wire:model="lineupForm.player_id">
                                    <option value="">-- {{ __('app.choose_player_lineup') }} --</option>
                                    @foreach($players as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->number ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-white-50">{{ __('app.position') }}</label>
                                <select class="form-select" style="background:#0d1117; color:#fff; border-color:rgba(255,255,255,0.1);" wire:model="lineupForm.position_id">
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
                                <label class="form-label fw-bold text-white-50">{{ __('app.jersey_number') }}</label>
                                <input type="number" class="form-control" style="background:#0d1117; color:#fff; border-color:rgba(255,255,255,0.1);" wire:model="lineupForm.jersey_number" min="0" max="99">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-white-50">{{ __('app.is_starter') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="lineupForm.is_starter" id="isStarterCheck">
                                    <label class="form-check-label fw-bold text-white" for="isStarterCheck">
                                        {{ $lineupForm['is_starter'] ? __('app.is_starter') : __('app.is_bench') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-white-50">{{ __('app.is_captain_label') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="lineupForm.is_captain" id="isCaptainCheck">
                                    <label class="form-check-label fw-bold text-white" for="isCaptainCheck">
                                        {{ $lineupForm['is_captain'] ? __('app.is_captain_label') : __('app.is_normal') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-sm btn-outline-info w-100" wire:click="$set('lineupForm.is_starter', true); $set('lineupForm.is_captain', false);" style="border-radius:8px;">
                                    <i class="bi bi-star"></i> {{ __('app.add_as_starter') }}
                                </button>
                            </div>

                            @if(!$lineupForm['is_starter'])
                                <div class="col-12"><hr style="border-color:rgba(255,255,255,0.1);"></div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-white-50">{{ __('app.minute_in') }}</label>
                                    <input type="number" class="form-control" style="background:#0d1117; color:#fff; border-color:rgba(255,255,255,0.1);" wire:model="lineupForm.minute_in" min="0" max="120">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-white-50">{{ __('app.minute_out') }}</label>
                                    <input type="number" class="form-control" style="background:#0d1117; color:#fff; border-color:rgba(255,255,255,0.1);" wire:model="lineupForm.minute_out" min="0" max="120">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-white-50">{{ __('app.sub_reason') }}</label>
                                    <select class="form-select" style="background:#0d1117; color:#fff; border-color:rgba(255,255,255,0.1);" wire:model="lineupForm.sub_reason">
                                        <option value="">-- {{ __('app.no_substitutes') }} --</option>
                                        @foreach($subReasons as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-12">
                                <label class="form-label fw-bold text-white-50">{{ __('app.performance_notes') }}</label>
                                <textarea class="form-control" style="background:#0d1117; color:#fff; border-color:rgba(255,255,255,0.1);" wire:model="lineupForm.performance_notes" rows="2" placeholder="{{ __('app.performance_notes_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top" style="border-color:rgba(255,255,255,0.1) !important;">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" style="border-radius:8px;">{{ __('app.cancel') }}</button>
                        <button type="button" class="btn btn-warning px-4" wire:click="saveLineup" wire:loading.attr="disabled" style="border-radius:8px;">
                            <span wire:loading.remove wire:target="saveLineup"><i class="bi bi-check-lg"></i> {{ __('app.save') }}</span>
                            <span wire:loading wire:target="saveLineup"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
