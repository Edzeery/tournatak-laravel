<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="breadcrumb-link">{{ __('app.teams') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.edit') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-pencil text-gold"></i> {{ __('app.edit') }} {{ __('app.team') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ $team->name }}</p>
        </div>
        <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary rounded-md">
            <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
        </a>
    </div>

    <nav class="nav nav-pills mb-3">
        <a class="nav-link active" href="{{ route('admin.teams.edit', $team->id) }}"><i class="bi bi-pencil"></i> {{ __('app.basic_data') }}</a>
        <a class="nav-link" href="{{ route('admin.teams.staff', $team->id) }}"><i class="bi bi-people"></i> {{ __('app.staff') }}</a>
        <a class="nav-link" href="{{ route('admin.teams.formations', $team->id) }}"><i class="bi bi-grid-3x3-gap"></i> {{ __('app.formations') }}</a>
        <a class="nav-link" href="{{ route('admin.teams.tactics', $team->id) }}"><i class="bi bi-diagram-3"></i> {{ __('app.tactics') }}</a>
        <a class="nav-link" href="{{ route('admin.teams.medical', $team->id) }}"><i class="bi bi-heart-pulse"></i> {{ __('app.medical_record') }}</a>
        <a class="nav-link" href="{{ route('admin.teams.stats', $team->id) }}"><i class="bi bi-bar-chart"></i> {{ __('app.team_stats') }}</a>
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
                        <label class="form-label fw-bold">{{ __('app.team_name') }}</label>
                        <input type="text" class="form-control" wire:model="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.captain') }}</label>
                        <select class="form-select" wire:model="captain_id">
                            <option value="">{{ __('app.choose_captain') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $team->captain_id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.logo_url') }}</label>
                        <input type="text" class="form-control" wire:model="logo" placeholder="https://...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.points') }}</label>
                        <input type="number" class="form-control" wire:model="points" min="0">
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
