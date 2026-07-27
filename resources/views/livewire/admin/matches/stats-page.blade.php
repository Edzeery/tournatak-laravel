<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.index') }}" class="text-decoration-none" style="color:var(--primary);">{{ __('app.matches') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.edit', $match) }}" class="text-decoration-none" style="color:var(--primary);">{{ $match->team1->name ?? '?' }} vs {{ $match->team2->name ?? '?' }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.match_stats') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-bar-chart-fill text-gold"></i> {{ __('app.match_stats') }}</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">
                {{ $match->team1->name ?? '?' }}
                <span class="fw-bold mx-1" style="color:#b8860b">vs</span>
                {{ $match->team2->name ?? '?' }}
            </p>
        </div>
        <a href="{{ route('admin.matches.edit', $match) }}" class="btn btn-outline-secondary" style="border-radius:8px;">
            <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
        </a>
    </div>

    {{-- Stats Comparison Bars --}}
    <div class="card border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold mb-0"><i class="bi bi-graph-up text-gold"></i> {{ __('app.visual_comparison') }}</h6>
        </div>
        <div class="card-body">
            @php
                $comparisonStats = [
                    'possession' => ['label' => __('app.possession'), 'max' => 100, 'suffix' => '%', 'isPercent' => true],
                    'shots_total' => ['label' => __('app.shots_total'), 'max' => null],
                    'shots_on_target' => ['label' => __('app.shots_on_target'), 'max' => null],
                    'corners' => ['label' => __('app.corners'), 'max' => null],
                    'fouls' => ['label' => __('app.fouls'), 'max' => null],
                    'offsides' => ['label' => __('app.offsides'), 'max' => null],
                    'yellow_cards' => ['label' => __('app.yellow_cards'), 'max' => null],
                    'red_cards' => ['label' => __('app.red_cards'), 'max' => null],
                    'passes_total' => ['label' => __('app.passes_total'), 'max' => null],
                    'passes_accurate' => ['label' => __('app.passes_accurate'), 'max' => null],
                    'tackles' => ['label' => __('app.tackles'), 'max' => null],
                    'saves' => ['label' => __('app.saves'), 'max' => null],
                ];
            @endphp

            @forelse($comparisonStats as $key => $stat)
                @php
                    $t1Val = $team1Stats->$key ?? 0;
                    $t2Val = $team2Stats->$key ?? 0;
                    if ($stat['isPercent'] ?? false) {
                        $maxVal = 100;
                    } else {
                        $maxVal = max($t1Val, $t2Val, 1);
                    }
                    $t1Width = round(($t1Val / $maxVal) * 100);
                    $t2Width = round(($t2Val / $maxVal) * 100);
                    $suffix = $stat['suffix'] ?? '';
                @endphp
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-1">
                        <div class="text-end" style="width:80px;">
                            <strong style="font-size:0.95rem;">{{ $t1Val }}{{ $suffix }}</strong>
                        </div>
                        <div class="flex-grow-1 px-3">
                            <div class="d-flex align-items-center gap-1" style="direction:ltr;">
                                <div class="flex-grow-1">
                                    <div class="progress" style="height:10px;border-radius:5px;">
                                        <div class="progress-bar bg-primary" style="width:{{ $t1Width }}%;border-radius:5px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center flex-shrink-0" style="width:140px;">
                            <small class="text-muted fw-bold" style="font-size:0.8rem;">{{ $stat['label'] }}</small>
                        </div>
                        <div class="flex-grow-1 px-3">
                            <div class="d-flex align-items-center gap-1" style="direction:ltr;">
                                <div class="flex-grow-1">
                                    <div class="progress" style="height:10px;border-radius:5px;">
                                        <div class="progress-bar bg-warning" style="width:{{ $t2Width }}%;border-radius:5px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-start" style="width:80px;">
                            <strong style="font-size:0.95rem;">{{ $t2Val }}{{ $suffix }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state py-3 text-center">
                    <i class="bi bi-bar-chart d-block text-muted" style="font-size:2rem;"></i>
                    <small class="text-muted">{{ __('app.no_stats_available') }}</small>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Editing Form --}}
    <div class="card border-0">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square text-gold"></i> {{ __('app.edit_stats') }}</h6>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Team Tabs --}}
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <button class="nav-link {{ $activeTeam == 1 ? 'active' : '' }}" wire:click="switchTeam(1)">
                        {{ $match->team1->name }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTeam == 2 ? 'active' : '' }}" wire:click="switchTeam(2)">
                        {{ $match->team2->name }}
                    </button>
                </li>
            </ul>

            <form wire:submit="saveStats">
                <div class="row g-3">
                    {{-- Possession --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.possession') }} (%)</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.possession' : 'statsForm2.possession' }}"
                               min="0" max="100" step="0.1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.possession') }} (%)</label>
                        @php
                            $otherPossession = $activeTeam == 1
                                ? ($statsForm2['possession'] ?? 50)
                                : ($statsForm['possession'] ?? 50);
                            $currentPossession = $activeTeam == 1
                                ? ($statsForm['possession'] ?? 50)
                                : ($statsForm2['possession'] ?? 50);
                        @endphp
                        <input type="number" class="form-control"
                               value="{{ 100 - ($activeTeam == 1 ? $otherPossession : $currentPossession) }}" disabled>
                        <small class="text-muted" style="font-size:0.75rem;">
                            {{ __('app.calculated_automatically') }} ({{ $activeTeam == 1 ? ($match->team2->name ?? __('app.team2_name')) : ($match->team1->name ?? __('app.team1_name')) }})
                        </small>
                    </div>

                    {{-- Shots --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">{{ __('app.shots_section') }}</small></div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.shots_total') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.shots_total' : 'statsForm2.shots_total' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.shots_on_target') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.shots_on_target' : 'statsForm2.shots_on_target' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.shots_off_target') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.shots_off_target' : 'statsForm2.shots_off_target' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.blocked_shots') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.blocked_shots' : 'statsForm2.blocked_shots' }}" min="0">
                    </div>

                    {{-- Set Pieces --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">{{ __('app.corners_fouls') }}</small></div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('app.corners') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.corners' : 'statsForm2.corners' }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('app.fouls') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.fouls' : 'statsForm2.fouls' }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('app.offsides') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.offsides' : 'statsForm2.offsides' }}" min="0">
                    </div>

                    {{-- Cards --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">{{ __('app.cards_section') }}</small></div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.yellow_cards') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.yellow_cards' : 'statsForm2.yellow_cards' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.red_cards') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.red_cards' : 'statsForm2.red_cards' }}" min="0">
                    </div>

                    {{-- Passing --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">{{ __('app.passes_section') }}</small></div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.passes_total') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.passes_total' : 'statsForm2.passes_total' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('app.passes_accurate') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.passes_accurate' : 'statsForm2.passes_accurate' }}" min="0">
                    </div>

                    {{-- Defense --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">{{ __('app.defense_section') }}</small></div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('app.tackles') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.tackles' : 'statsForm2.tackles' }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('app.saves') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.saves' : 'statsForm2.saves' }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('app.hit_woodwork') }}</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.hit_woodwork' : 'statsForm2.hit_woodwork' }}" min="0">
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <small class="text-muted" style="font-size:0.8rem;">
                        <i class="bi bi-info-circle"></i>
                        {{ __('app.stats_for_team') }} {{ $activeTeam == 1 ? ($match->team1->name ?? __('app.team1_name')) : ($match->team2->name ?? __('app.team2_name')) }}
                    </small>
                    <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveStats"><i class="bi bi-check-lg"></i> {{ __('app.save_stats') }}</span>
                        <span wire:loading wire:target="saveStats"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Section --}}
    <div class="card border-0 mt-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold mb-0"><i class="bi bi-clipboard-data text-gold"></i> {{ __('app.match_summary') }}</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <div class="border rounded-3 p-3" style="background:#f8f9fa;">
                        <h3 class="fw-bold text-primary mb-1">{{ $match->score_team1 ?? 0 }}</h3>
                        <small class="text-muted fw-bold">{{ $match->team1->name ?? __('app.team1_name') }}</small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded-3 p-3" style="background:#f8f9fa;">
                        <small class="text-muted d-block mb-1">{{ __('app.final_score') }}</small>
                        <h3 class="fw-bold mb-0" style="color:#b8860b">{{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded-3 p-3" style="background:#f8f9fa;">
                        <h3 class="fw-bold text-warning mb-1">{{ $match->score_team2 ?? 0 }}</h3>
                        <small class="text-muted fw-bold">{{ $match->team2->name ?? __('app.team2_name') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
