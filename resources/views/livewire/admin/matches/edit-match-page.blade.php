<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.index') }}" class="breadcrumb-link">{{ __('app.matches') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.edit_match') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-pencil text-gold"></i> {{ __('app.edit_match') }}</h4>
        </div>
        <a href="{{ route('admin.matches.index') }}" class="btn btn-outline-secondary rounded-md">
            <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
        </a>
    </div>

    <nav class="nav nav-pills mb-3">
        <a class="nav-link active" href="{{ route('admin.matches.edit', $match->id) }}"><i class="bi bi-pencil"></i> {{ __('app.basic_data') }}</a>
        <a class="nav-link" href="{{ route('admin.matches.lineup', $match->id) }}"><i class="bi bi-people-fill"></i> {{ __('app.lineup') }}</a>
        <a class="nav-link" href="{{ route('admin.matches.events', $match->id) }}"><i class="bi bi-clock-history"></i> {{ __('app.match_events') }}</a>
        <a class="nav-link" href="{{ route('admin.matches.stats', $match->id) }}"><i class="bi bi-bar-chart-line"></i> {{ __('app.match_stats') }}</a>
    </nav>

    <div class="card border-0">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form wire:submit="update">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.competition') }}</label>
                        <select class="form-select" wire:model="competition_id" required>
                            @foreach($competitions as $competition)
                                <option value="{{ $competition->id }}" {{ $competition->id == $match->competition_id ? 'selected' : '' }}>{{ $competition->name }}</option>
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
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ $team->id == $match->team1_id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.team2') }}</label>
                        <select class="form-select" wire:model="team2_id" required>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ $team->id == $match->team2_id ? 'selected' : '' }}>{{ $team->name }}</option>
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
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('app.score_team1') }}</label>
                        <input type="number" class="form-control" wire:model="score_team1" min="0" value="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('app.score_team2') }}</label>
                        <input type="number" class="form-control" wire:model="score_team2" min="0" value="0">
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
                                <option value="{{ $ref->id }}" @selected($ref->id === $referee_id)>{{ $ref->name }} @if($ref->license_number)({{ $ref->license_number }})@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.assistant_referee_1') }}</label>
                        <select class="form-select" wire:model="assistant_referee_1_id">
                            <option value="">{{ __('app.choose_referee') }}</option>
                            @foreach($referees->where('specialization', 'assistant_referee') as $ref)
                                <option value="{{ $ref->id }}" @selected($ref->id === $assistant_referee_1_id)>{{ $ref->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.assistant_referee_2') }}</label>
                        <select class="form-select" wire:model="assistant_referee_2_id">
                            <option value="">{{ __('app.choose_referee') }}</option>
                            @foreach($referees->where('specialization', 'assistant_referee') as $ref)
                                <option value="{{ $ref->id }}" @selected($ref->id === $assistant_referee_2_id)>{{ $ref->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.fourth_official') }}</label>
                        <select class="form-select" wire:model="fourth_official_id">
                            <option value="">{{ __('app.choose_referee') }}</option>
                            @foreach($referees->where('specialization', 'fourth_official') as $ref)
                                <option value="{{ $ref->id }}" @selected($ref->id === $fourth_official_id)>{{ $ref->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="update"><i class="bi bi-check-lg"></i> {{ __('app.save_changes') }}</span>
                    <span wire:loading wire:target="update"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
