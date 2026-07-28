<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="breadcrumb-link">{{ __('app.teams') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.edit', $team) }}" class="breadcrumb-link">{{ $team->name }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.formations') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark-theme"><i class="bi bi-diagram-3-fill text-gold"></i> {{ __('app.formations') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ $team->name }}</p>
        </div>
        <button class="btn btn-warning" wire:click="openModal">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_formation') }}
        </button>
    </div>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_by_name_or_code') }}" wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3" wire:loading.opacity>
        @forelse($formations as $formation)
            @php
                $positions = is_array($formation->positions_data) ? $formation->positions_data : json_decode($formation->positions_data ?? '[]', true);
            @endphp
            <div class="col-md-6 col-lg-4" wire:key="{{ $formation->id }}">
                <div class="card border-0 h-100 rounded-lg-custom shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $formation->name }}</h6>
                                <span class="badge bg-primary-subtle text-primary fw-bold fs-08">{{ $formation->formation_code }}</span>
                            </div>
                            <div class="d-flex gap-1">
                                <span class="badge {{ $formation->sport_type === 'football' ? 'bg-success' : 'bg-info' }} fs-xs">

                                    {{ $formation->sport_type === 'football' ? __('app.football') : __('app.futsal') }}
                                </span>
                                @if($formation->is_default)
                                    <span class="badge bg-warning text-dark fs-xs">
                                        <i class="bi bi-star-fill"></i> {{ __('app.default_badge') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="text-center my-2">
                            <svg viewBox="0 0 200 140" style="width:100%;max-width:280px;height:auto;">
                                <defs>
                                    <linearGradient id="pitchGrad{{ $formation->id }}" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" style="stop-color:#2d8a4e;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#1e6b36;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <rect x="5" y="5" width="190" height="130" rx="4" fill="url(#pitchGrad{{ $formation->id }})" stroke="#fff" stroke-width="1" opacity="0.95"/>
                                <line x1="100" y1="5" x2="100" y2="135" stroke="#fff" stroke-width="0.5" opacity="0.4"/>
                                <circle cx="100" cy="70" r="18" fill="none" stroke="#fff" stroke-width="0.5" opacity="0.4"/>
                                <rect x="60" y="5" width="80" height="30" rx="0" fill="none" stroke="#fff" stroke-width="0.5" opacity="0.4"/>
                                <rect x="60" y="105" width="80" height="30" rx="0" fill="none" stroke="#fff" stroke-width="0.5" opacity="0.4"/>
                                @foreach($positions as $pos)
                                    <circle cx="{{ ($pos['x'] / 100) * 190 + 5 }}" cy="{{ ($pos['y'] / 100) * 130 + 5 }}" r="5" fill="#fff" stroke="#f0c040" stroke-width="1.5"/>
                                @endforeach
                            </svg>
                        </div>

                        @if($formation->description)
                            <p class="text-muted mb-2 fs-08">{{ Str::limit($formation->description, 80) }}</p>
                        @endif

                        <div class="d-flex gap-1 justify-content-end pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary rounded-md" wire:click="editFormation({{ $formation->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger rounded-md"
                                    wire:click="deleteFormation({{ $formation->id }})"
                                    wire:confirm="{{ __('app.confirm_delete_formation') }}">
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
                            <i class="bi bi-diagram-3 d-block fs-4xl"></i>
                            <h5>{{ __('app.no_formations') }}</h5>
                            <p class="text-muted">{{ __('app.no_formations_desc') }}</p>
                            <button class="btn btn-warning" wire:click="openModal">
                                <i class="bi bi-plus-lg"></i> {{ __('app.add_formation') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($showModal)
        <div class="modal fade show d-block modal-overlay-blur" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="formationModalTitle" wire:click.self="closeModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-xl" wire:click.stop>
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="formationModalTitle">
                            <i class="bi bi-diagram-3-fill text-gold"></i>
                            {{ $editingFormationId ? __('app.edit_formation') : __('app.add_formation') }}
                        </h5>
                        <button type="button" class="btn-close" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.formation_name') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('app.formation_name_placeholder') }}" wire:model="formationForm.name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.sport_type') }}</label>
                                <select class="form-select" wire:model.live="formationForm.sport_type">
                                    <option value="football">{{ __('app.football') }}</option>
                                    <option value="futsal">{{ __('app.futsal') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.formation_code') }}</label>
                                <select class="form-select" wire:model.live="formationForm.formation_code">
                                    @if($formationForm['sport_type'] === 'football')
                                        @foreach($footballFormations as $fc)
                                            <option value="{{ $fc }}">{{ $fc }}</option>
                                        @endforeach
                                    @else
                                        @foreach($futsalFormations as $fc)
                                            <option value="{{ $fc }}">{{ $fc }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.default_formation') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="formationForm.is_default" id="isDefaultFormation">
                                    <label class="form-check-label fw-bold" for="isDefaultFormation">{{ __('app.default_formation') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.description') }}</label>
                            <textarea class="form-control" rows="2" placeholder="{{ __('app.description_placeholder') }}" wire:model="formationForm.description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.formation_preview') }}</label>
                            <div class="text-center p-3 bg-light rounded" style="border:1px dashed #ccc;">
                                <svg viewBox="0 0 200 140" style="width:100%;max-width:360px;height:auto;">
                                    <defs>
                                        <linearGradient id="modalPitchGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" style="stop-color:#2d8a4e;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#1e6b36;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                    <rect x="5" y="5" width="190" height="130" rx="4" fill="url(#modalPitchGrad)" stroke="#fff" stroke-width="1"/>
                                    <line x1="100" y1="5" x2="100" y2="135" stroke="#fff" stroke-width="0.5" opacity="0.4"/>
                                    <circle cx="100" cy="70" r="18" fill="none" stroke="#fff" stroke-width="0.5" opacity="0.4"/>
                                    <rect x="60" y="5" width="80" height="30" rx="0" fill="none" stroke="#fff" stroke-width="0.5" opacity="0.4"/>
                                    <rect x="60" y="105" width="80" height="30" rx="0" fill="none" stroke="#fff" stroke-width="0.5" opacity="0.4"/>
                                    @foreach($selectedPositions as $pos)
                                        <circle cx="{{ ($pos['x'] / 100) * 190 + 5 }}" cy="{{ ($pos['y'] / 100) * 130 + 5 }}" r="5" fill="#fff" stroke="#f0c040" stroke-width="1.5"/>
                                    @endforeach
                                </svg>
                                <div class="mt-2">
                                    <span class="badge bg-primary fw-bold">{{ $formationForm['formation_code'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary rounded-md" wire:click="closeModal">{{ __('app.cancel') }}</button>
                        <button type="button" class="btn btn-warning px-4 rounded-md" wire:click="saveFormation" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveFormation"><i class="bi bi-check-lg"></i> {{ __('app.save') }}</span>
                            <span wire:loading wire:target="saveFormation"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
