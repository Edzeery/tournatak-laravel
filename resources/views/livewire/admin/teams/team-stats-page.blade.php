<div>
    <x-section-header
        icon="bi bi-bar-chart-line-fill"
        :title="__('app.team_stats')"
        :subtitle="$team->name"
        :breadcrumbs="[
            ['route' => route('admin.dashboard'), 'label' => __('app.dashboard')],
            ['route' => route('admin.teams.index'), 'label' => __('app.teams')],
            ['route' => route('admin.teams.edit', $team), 'label' => $team->name],
            ['label' => __('app.team_stats')],
        ]"
    >
        <x-slot:action>
            <div class="d-flex gap-2">
                <button class="btn btn-warning px-3 rounded-md" wire:click="openModal">
                    <i class="bi bi-plus-lg"></i> {{ __('app.add_stat') }}
                </button>
                <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-outline-secondary rounded-md">
                    <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
                </a>
            </div>
        </x-slot:action>
    </x-section-header>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($availableSeasons->isNotEmpty())
        <div class="card border-0 mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-base">{{ __('app.season') }}</label>
                        <select class="form-select" wire:model.live="selectedSeason" aria-label="{{ __('app.per_page') }}">
                            @foreach($availableSeasons as $season)
                                <option value="{{ $season }}">{{ $season }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        @php
            $totalMatches = $this->totalMatchesPlayed;
            $totalWins = $this->totalWins;
            $totalDraws = $this->totalDraws;
            $totalLosses = $this->totalLosses;
            $totalGoalsFor = $this->totalGoalsFor;
            $totalPoints = $this->totalPoints;
        @endphp
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center rounded-lg-custom shadow-sm">
                <div class="card-body py-3">
                    <i class="bi bi-calendar-event text-gold fs-xl"></i>
                    <div class="fw-bold mt-1 fs-2xl">{{ $totalMatches }}</div>
                    <small class="text-muted fw-bold">{{ __('app.matches_played') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center rounded-lg-custom shadow-sm">
                <div class="card-body py-3">
                    <i class="bi bi-trophy text-gold fs-xl"></i>
                    <div class="fw-bold mt-1 fs-2xl text-success">{{ $totalWins }}</div>
                    <small class="text-muted fw-bold">{{ __('app.wins') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center rounded-lg-custom shadow-sm">
                <div class="card-body py-3">
                    <i class="bi bi-handshake text-gold fs-xl"></i>
                    <div class="fw-bold mt-1 fs-2xl text-warning">{{ $totalDraws }}</div>
                    <small class="text-muted fw-bold">{{ __('app.draws_stat') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center rounded-lg-custom shadow-sm">
                <div class="card-body py-3">
                    <i class="bi bi-x-circle text-gold fs-xl"></i>
                    <div class="fw-bold mt-1 fs-2xl text-danger">{{ $totalLosses }}</div>
                    <small class="text-muted fw-bold">{{ __('app.losses_stat') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center rounded-lg-custom shadow-sm">
                <div class="card-body py-3">
                    <i class="bi bi-bullseye text-gold fs-xl"></i>
                    <div class="fw-bold mt-1 fs-2xl text-blue">{{ $totalGoalsFor }}</div>
                    <small class="text-muted fw-bold">{{ __('app.goals') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center rounded-lg-custom shadow-sm">
                <div class="card-body py-3">
                    <i class="bi bi-star-fill text-gold fs-xl"></i>
                    <div class="fw-bold mt-1 fs-2xl" style="color:#0dcaf0;">{{ $totalPoints }}</div>
                    <small class="text-muted fw-bold">{{ __('app.points') }}</small>
                </div>
            </div>
        </div>
    </div>

    @if($totalMatches > 0)
        <div class="card border-0 mb-4">
            <div class="card-header border-0 fw-bold bg-transparent pt-3">
                <i class="bi bi-pie-chart text-gold"></i> {{ __('app.results_percentages') }}
            </div>
            <div class="card-body">
                @php
                    $winPct = $totalMatches > 0 ? round(($totalWins / $totalMatches) * 100) : 0;
                    $drawPct = $totalMatches > 0 ? round(($totalDraws / $totalMatches) * 100) : 0;
                    $lossPct = $totalMatches > 0 ? round(($totalLosses / $totalMatches) * 100) : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold fs-base">{{ __('app.wins') }}</span>
                        <span class="fw-bold fs-base text-success">{{ $winPct }}%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success progress-fill" style="width:{{ $winPct }}%;"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold fs-base">{{ __('app.draws_stat') }}</span>
                        <span class="fw-bold fs-base text-warning">{{ $drawPct }}%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-warning progress-fill" style="width:{{ $drawPct }}%;"></div>
                    </div>
                </div>
                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold fs-base">{{ __('app.losses_stat') }}</span>
                        <span class="fw-bold fs-base text-danger">{{ $lossPct }}%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-danger progress-fill" style="width:{{ $lossPct }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0" wire:loading.opacity>
        <div class="card-header border-0 fw-bold bg-transparent pt-3">
            <i class="bi bi-table text-gold"></i> {{ __('app.stats_by_competition') }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('app.competition') }}</th>
                            <th>{{ __('app.season') }}</th>
                            <th>{{ __('app.matches_played') }}</th>
                            <th>{{ __('app.wins') }}</th>
                            <th>{{ __('app.draws_stat') }}</th>
                            <th>{{ __('app.losses_stat') }}</th>
                            <th>{{ __('app.goals') }}</th>
                            <th>{{ __('app.points') }}</th>
                            <th>{{ __('app.possession') }}</th>
                            <th>{{ __('app.yellow_cards') }}</th>
                            <th>{{ __('app.red_cards') }}</th>
                            <th>{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($seasonStats as $stat)
                            <tr wire:key="{{ $stat->id }}">
                                <td class="fw-bold">{{ $stat->competition->name ?? '—' }}</td>
                                <td class="fs-base text-theme-muted">{{ $stat->season_year }}</td>
                                <td>{{ $stat->matches_played }}</td>
                                <td><span class="fw-bold text-success">{{ $stat->wins }}</span></td>
                                <td><span class="fw-bold text-warning">{{ $stat->draws }}</span></td>
                                <td><span class="fw-bold text-danger">{{ $stat->losses }}</span></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-bold">
                                        {{ $stat->goals_for }} - {{ $stat->goals_against }}
                                    </span>
                                </td>
                                <td><span class="badge bg-warning text-dark fw-bold">{{ $stat->points }}</span></td>
                                <td class="fs-base">{{ $stat->possession_avg ?? '—' }}%</td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning fw-bold">{{ $stat->yellow_cards ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger fw-bold">{{ $stat->red_cards ?? 0 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary" wire:click="editStat({{ $stat->id }})" title="{{ __('app.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" wire:click="deleteStat({{ $stat->id }})" wire:confirm="{{ __('app.confirm_delete_stat') }}" title="{{ __('app.delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12">
                                    <div class="empty-state py-3">
                                        <i class="bi bi-bar-chart d-block fs-4xl"></i>
                                        <h5>{{ __('app.no_stats') }}</h5>
                                        <p class="text-muted">{{ __('app.no_stats_desc') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($showModal)
        <div class="modal fade show d-block modal-backdrop-dark" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="statModalTitle">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-lg-custom">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="statModalTitle">
                            <i class="bi bi-bar-chart-line text-gold"></i>
                            {{ $editingStatId ? __('app.edit_stat') : __('app.add_new_stat') }}
                        </h5>
                        <button type="button" class="btn-close" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('app.competition') }} <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="statForm.competition_id">
                                    <option value="">-- {{ __('app.choose_competition') }} --</option>
                                    @foreach($availableCompetitions as $comp)
                                        <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                                    @endforeach
                                </select>
                                @error('statForm.competition_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('app.season') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" wire:model="statForm.season_year" min="2000" max="2100">
                                @error('statForm.season_year') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">{{ __('app.matches_and_results') }}</small></div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.matches_played') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.matches_played" min="0">
                                @error('statForm.matches_played') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.wins') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.wins" min="0">
                                @error('statForm.wins') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.draws_stat') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.draws" min="0">
                                @error('statForm.draws') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.losses_stat') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.losses" min="0">
                                @error('statForm.losses') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.goals_for') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.goals_for" min="0">
                                @error('statForm.goals_for') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.goals_against') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.goals_against" min="0">
                                @error('statForm.goals_against') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.clean_sheets') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.clean_sheets" min="0">
                                @error('statForm.clean_sheets') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.points') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.points" min="0">
                                @error('statForm.points') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">{{ __('app.detailed_stats') }}</small></div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.yellow_cards') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.yellow_cards" min="0">
                                @error('statForm.yellow_cards') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.red_cards') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.red_cards" min="0">
                                @error('statForm.red_cards') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.avg_possession') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.possession_avg" min="0" max="100" step="0.1">
                                @error('statForm.possession_avg') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.shots_per_match') }}</label>
                                <input type="number" class="form-control" wire:model="statForm.shots_per_match" min="0" step="0.1">
                                @error('statForm.shots_per_match') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">{{ __('app.cancel') }}</button>
                        <button type="button" class="btn btn-warning px-4" wire:click="saveStat" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveStat">
                                <i class="bi bi-check-lg"></i> {{ $editingStatId ? __('app.update') : __('app.save') }}
                            </span>
                            <span wire:loading wire:target="saveStat">
                                <span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
