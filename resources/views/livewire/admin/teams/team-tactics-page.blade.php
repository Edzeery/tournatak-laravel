<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="breadcrumb-link">{{ __('app.teams') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.edit', $team) }}" class="breadcrumb-link">{{ $team->name }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.tactics') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark-theme"><i class="bi bi-lightning-charge-fill text-gold"></i> {{ __('app.tactics') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ $team->name }}</p>
        </div>
        <button class="btn btn-warning" wire:click="openModal">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_tactic') }}
        </button>
    </div>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_by_name_or_formation') }}" wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3" wire:loading.opacity>
        @forelse($tactics as $tactic)
            <div class="col-md-6 col-lg-4" wire:key="{{ $tactic->id }}">
                <div class="card border-0 h-100 rounded-lg-custom shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0">{{ $tactic->name }}</h6>
                            @if($tactic->is_default)
                                <span class="badge bg-warning text-dark fs-xs">
                                    <i class="bi bi-star-fill"></i> {{ __('app.default_badge') }}
                                </span>
                            @endif
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-danger-subtle text-danger fw-bold me-1 mb-1 fs-xs">
                                <i class="bi bi-speedometer"></i> {{ $pressingStyles[$tactic->pressing_style] ?? $tactic->pressing_style }}
                            </span>
                            <span class="badge bg-primary-subtle text-primary fw-bold me-1 mb-1 fs-xs">
                                <i class="bi bi-arrows-angle-expand"></i> {{ $buildUpStyles[$tactic->build_up_style] ?? $tactic->build_up_style }}
                            </span>
                            <span class="badge bg-success-subtle text-success fw-bold me-1 mb-1 fs-xs">
                                <i class="bi bi-shield-fill"></i> {{ $defenseStyles[$tactic->defense_style] ?? $tactic->defense_style }}
                            </span>
                            <span class="badge bg-warning-subtle text-warning fw-bold me-1 mb-1 fs-xs">
                                <i class="bi bi-lightning"></i> {{ $attackStyles[$tactic->attack_style] ?? $tactic->attack_style }}
                            </span>
                        </div>

                        @if($tactic->formation_used)
                            <div class="mb-2">
                                <span class="badge bg-info-subtle text-info fw-bold fs-sm">
                                    <i class="bi bi-diagram-3"></i> {{ $tactic->formation_used }}
                                </span>
                            </div>
                        @endif

                        @if($tactic->notes)
                            <p class="text-muted mb-2 fs-08">{{ Str::limit($tactic->notes, 100) }}</p>
                        @endif

                        <div class="d-flex gap-1 justify-content-end pt-2 border-top mt-auto">
                            <button class="btn btn-sm btn-outline-primary rounded-md" wire:click="editTactic({{ $tactic->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger rounded-md"
                                    wire:click="deleteTactic({{ $tactic->id }})"
                                    wire:confirm="{{ __('app.confirm_delete_tactic') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-body py-5 text-center">
                        <div class="empty-state py-3">
                            <i class="bi bi-lightning-charge d-block fs-4xl"></i>
                            <h5>{{ __('app.no_tactics') }}</h5>
                            <p class="text-muted">{{ __('app.no_tactics_desc') }}</p>
                            <button class="btn btn-warning" wire:click="openModal">
                                <i class="bi bi-plus-lg"></i> {{ __('app.add_tactic') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($showModal)
        <div class="modal fade show d-block modal-overlay-blur" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="tacticModalTitle" wire:click.self="closeModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-xl" wire:click.stop>
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="tacticModalTitle">
                            <i class="bi bi-lightning-charge-fill text-gold"></i>
                            {{ $editingTacticId ? __('app.edit_tactic') : __('app.add_tactic') }}
                        </h5>
                        <button type="button" class="btn-close" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.tactic_name') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('app.tactic_name_placeholder') }}" wire:model="tacticForm.name">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.pressing_style') }}</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($pressingStyles as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="pressing_style" id="pressing_{{ $key }}" value="{{ $key }}" wire:model="tacticForm.pressing_style">
                                            <label class="form-check-label fw-bold" for="pressing_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.build_up_style') }}</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($buildUpStyles as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="build_up_style" id="buildup_{{ $key }}" value="{{ $key }}" wire:model="tacticForm.build_up_style">
                                            <label class="form-check-label fw-bold" for="buildup_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.defense_style_label') }}</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($defenseStyles as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="defense_style" id="defense_{{ $key }}" value="{{ $key }}" wire:model="tacticForm.defense_style">
                                            <label class="form-check-label fw-bold" for="defense_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.attack_style') }}</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($attackStyles as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="attack_style" id="attack_{{ $key }}" value="{{ $key }}" wire:model="tacticForm.attack_style">
                                            <label class="form-check-label fw-bold" for="attack_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.formation_used') }}</label>
                                <select class="form-select" wire:model="tacticForm.formation_used">
                                    <option value="">{{ __('app.no_formation') }}</option>
                                    @foreach($formationOptions as $fo)
                                        <option value="{{ $fo }}">{{ $fo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.default_tactic') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="tacticForm.is_default" id="isDefaultTactic">
                                    <label class="form-check-label fw-bold" for="isDefaultTactic">{{ __('app.default_tactic') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.tactic_notes') }}</label>
                            <textarea class="form-control" rows="3" placeholder="{{ __('app.tactic_notes_placeholder') }}" wire:model="tacticForm.notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary rounded-md" wire:click="closeModal">{{ __('app.cancel') }}</button>
                        <button type="button" class="btn btn-warning px-4 rounded-md" wire:click="saveTactic" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveTactic"><i class="bi bi-check-lg"></i> {{ __('app.save') }}</span>
                            <span wire:loading wire:target="saveTactic"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
