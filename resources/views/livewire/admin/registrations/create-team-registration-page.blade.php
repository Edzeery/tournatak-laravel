<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.registrations.index') }}" class="breadcrumb-link">{{ __('app.registrations') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.add_new') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-shield-fill text-gold"></i> {{ __('app.add_team_registration') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ __('app.team_registration_desc') }}</p>
        </div>
        <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary rounded-md">
            <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
        </a>
    </div>

    <div class="card border-0">
        <div class="card-body p-4">
            <form wire:submit="store">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.competition') }} <span class="text-danger">*</span></label>
                        <select class="form-select" wire:model="competition_id" required>
                            <option value="">{{ __('app.choose_competition') }}</option>
                            @foreach($competitions as $competition)
                                <option value="{{ $competition->id }}">
                                    {{ $competition->name }}
                                    @if($competition->type)
                                        ({{ $competition->type->name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('competition_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.team') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2"
                            placeholder="{{ __('app.search_team_placeholder') }}"
                            wire:model.live.debounce.300ms="searchTeam">
                        <select class="form-select" wire:model="team_id" required size="5">
                            <option value="">{{ __('app.choose_team') }}</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('team_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_registration') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
