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

                    {{-- Logo --}}
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">{{ __('app.logo') }}</label>

                        {{-- Current logo preview --}}
                        @if($logo && !$removeLogo)
                            <div class="d-flex align-items-center gap-3 mb-3 p-3 border rounded">
                                <img src="{{ $team->logo_url }}" alt="" class="rounded-circle object-cover border-chrome w-80 h-80 logo-ring">
                                <div>
                                    <div class="fw-bold">{{ __('app.current_logo') }}</div>
                                    <label class="d-flex align-items-center gap-1 cursor-pointer mt-1 text-danger">
                                        <input type="checkbox" wire:model.live="removeLogo">
                                        <i class="bi bi-trash"></i> {{ __('app.remove_logo') }}
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex gap-3 mb-2">
                            <label class="d-flex align-items-center gap-1 cursor-pointer">
                                <input type="radio" wire:model.live="logoSrc" value="upload">
                                <span>{{ __('app.upload') }}</span>
                            </label>
                            <label class="d-flex align-items-center gap-1 cursor-pointer">
                                <input type="radio" wire:model.live="logoSrc" value="url">
                                <span>{{ __('app.link') }}</span>
                            </label>
                            @if(!$logo || $removeLogo)
                                <label class="d-flex align-items-center gap-1 cursor-pointer">
                                    <input type="radio" wire:model.live="logoSrc" value="none">
                                    <span>{{ __('app.none') }}</span>
                                </label>
                            @endif
                        </div>

                        @if ($logoSrc === 'upload')
                            <input type="file" class="form-control" wire:model="logoFile" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                            <div class="fs-sm text-chrome-muted mt-1">
                                <i class="bi bi-info-circle"></i> {{ __('app.logo_constraints') }}
                            </div>
                            <div wire:loading wire:target="logoFile" class="fs-sm text-warning mt-1">
                                <span class="spinner-border spinner-border-sm"></span> {{ __('app.uploading') }}...
                            </div>
                            @if ($logoFile)
                                <div class="mt-2">
                                    <img src="{{ $logoFile->temporaryUrl() }}" alt="" class="rounded-circle object-cover border-chrome w-64 h-64 logo-ring">
                                </div>
                            @endif
                        @elseif ($logoSrc === 'url')
                            <input type="text" class="form-control" wire:model="logoUrl" placeholder="https://...">
                        @endif
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
