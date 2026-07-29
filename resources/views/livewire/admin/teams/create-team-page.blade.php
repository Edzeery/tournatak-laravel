<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="breadcrumb-link">{{ __('app.teams') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.add_new') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-plus-circle text-gold"></i> {{ __('app.add_team') }} {{ __('app.new') }}</h4>
        </div>
        <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary rounded-md">
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
                        <label class="form-label fw-bold">{{ __('app.team_name') }}</label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="{{ __('app.team_name') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.captain') }}</label>
                        <select class="form-select" wire:model="captain_id">
                            <option value="">{{ __('app.choose_captain') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">{{ __('app.logo') }}</label>
                        <div class="d-flex gap-3 mb-2">
                            <label class="d-flex align-items-center gap-1 cursor-pointer">
                                <input type="radio" wire:model="logoSrc" value="upload">
                                <span>{{ __('app.upload') }}</span>
                            </label>
                            <label class="d-flex align-items-center gap-1 cursor-pointer">
                                <input type="radio" wire:model="logoSrc" value="url">
                                <span>{{ __('app.link') }}</span>
                            </label>
                            <label class="d-flex align-items-center gap-1 cursor-pointer">
                                <input type="radio" wire:model="logoSrc" value="none" checked>
                                <span>{{ __('app.none') }}</span>
                            </label>
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
                        <input type="number" class="form-control" wire:model="points" min="0" value="0">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_team') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
