<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.index') }}" class="breadcrumb-link">{{ __('app.matches') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.add_new') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-plus-circle text-gold"></i> {{ __('app.add_new_match') }}</h4>
        </div>
        <a href="{{ route('admin.matches.index') }}" class="btn btn-outline-secondary rounded-md">
            <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
        </a>
    </div>

    <div class="card border-0">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form wire:submit="store">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.competition') }}</label>
                        <select class="form-select" wire:model="competition_id" required>
                            <option value="">{{ __('app.choose_competition') }}</option>
                            @foreach($competitions as $competition)
                                <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.match_date') }}</label>
                        <input type="text" class="form-control flatpickr-input" wire:model="match_date" placeholder="{{ __('app.select_date_time') }}" data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.team1') }}</label>
                        <select class="form-select" wire:model="team1_id" required>
                            <option value="">{{ __('app.choose_team') }}</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.team2') }}</label>
                        <select class="form-select" wire:model="team2_id" required>
                            <option value="">{{ __('app.choose_team') }}</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.status') }}</label>
                        <select class="form-select" wire:model="status">
                            <option value="scheduled">{{ __('app.status_scheduled_option') }}</option>
                            <option value="live">{{ __('app.status_live') }}</option>
                            <option value="finished">{{ __('app.status_finished') }}</option>
                            <option value="cancelled">{{ __('app.status_cancelled') }}</option>
                        </select>
                    </div>

                    {{-- Referees --}}
                    <div class="col-12 mt-2 mb-2">
                        <hr>
                        <h6 class="fw-bold text-theme-primary"><i class="bi bi-person-check-fill text-gold"></i> {{ __('app.referee_team') }}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.main_referee') }}</label>
                        <select class="form-select" wire:model="referee_id">
                            <option value="">{{ __('app.choose_referee') }}</option>
                            @foreach($referees->where('specialization', 'referee') as $ref)
                                <option value="{{ $ref->id }}">{{ $ref->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.assistant_referee_1') }}</label>
                        <select class="form-select" wire:model="assistant_referee_1_id">
                            <option value="">{{ __('app.choose_referee') }}</option>
                            @foreach($referees->where('specialization', 'assistant_referee') as $ref)
                                <option value="{{ $ref->id }}">{{ $ref->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.assistant_referee_2') }}</label>
                        <select class="form-select" wire:model="assistant_referee_2_id">
                            <option value="">{{ __('app.choose_referee') }}</option>
                            @foreach($referees->where('specialization', 'assistant_referee') as $ref)
                                <option value="{{ $ref->id }}">{{ $ref->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.fourth_official') }}</label>
                        <select class="form-select" wire:model="fourth_official_id">
                            <option value="">{{ __('app.choose_referee') }}</option>
                            @foreach($referees->where('specialization', 'fourth_official') as $ref)
                                <option value="{{ $ref->id }}">{{ $ref->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_match') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
