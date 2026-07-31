<div>
    <x-section-header
        :title="__('app.add_team').' '.__('app.new')"
        icon="bi-plus-circle"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.teams'), 'route' => route('admin.teams.index')],
            ['label' => __('app.add_new')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary rounded-md">
                <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
            </a>
        </x-slot:action>
    </x-section-header>

    <div class="card border-0">
        <div class="card-body p-4">
            <x-form-errors />

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
