<div>
    <x-section-header
        icon="bi bi-shield-fill"
        :title="__('app.add_team_registration')"
        :subtitle="__('app.team_registration_desc')"
        :breadcrumbs="[
            ['route' => route('admin.dashboard'), 'label' => __('app.dashboard')],
            ['route' => route('admin.registrations.index'), 'label' => __('app.registrations')],
            ['label' => __('app.add_new')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary rounded-md">
                <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
            </a>
        </x-slot:action>
    </x-section-header>

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
