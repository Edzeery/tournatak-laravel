<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.players.index') }}" class="breadcrumb-link">{{ __('app.players') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.edit_player') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-pencil text-gold"></i> {{ __('app.edit_player') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ $player->user->name ?? '' }}</p>
        </div>
        <a href="{{ route('admin.players.index') }}" class="btn btn-outline-secondary rounded-md">
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

            <form wire:submit="update">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.user') }}</label>
                        <select class="form-select" wire:model="user_id" required>
                            <option value="">{{ __('app.choose_user') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $player->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.team') }}</label>
                        <select class="form-select" wire:model="team_id" required>
                            <option value="">{{ __('app.choose_team') }}</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ $team->id == $player->team_id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.player_number') }}</label>
                        <input type="number" class="form-control" wire:model="number" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.position') }}</label>
                        <input type="text" class="form-control" wire:model="position_text" placeholder="{{ __('app.position_placeholder') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.image') }}</label>
                        <div class="d-flex gap-2 mb-2">
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="imageSrc" value="url">
                                <span class="form-check-label">{{ __('app.link') }}</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="imageSrc" value="upload">
                                <span class="form-check-label">{{ __('app.upload') }}</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="imageSrc" value="none">
                                <span class="form-check-label">{{ __('app.no_image') }}</span>
                            </label>
                        </div>
                        @if($imageSrc === 'url')
                            <input type="text" class="form-control" wire:model="image" placeholder="https://...">
                        @elseif($imageSrc === 'upload')
                            <input type="file" class="form-control" wire:model="imageFile" accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="text-white-50">{{ __('app.logo_constraints') }}</small>
                            @if($imageFile)
                                <div class="mt-2">
                                    <img src="{{ $imageFile->temporaryUrl() }}" alt="" class="rounded-circle object-cover" width="64" height="64">
                                </div>
                            @endif
                        @endif
                        @if($player->image && !$removeImage && $imageSrc !== 'none')
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="{{ $player->image_url }}" alt="" class="rounded-circle object-cover" width="48" height="48">
                                <small class="text-white-50">{{ __('app.current_image') }}</small>
                                <label class="form-check ms-auto">
                                    <input class="form-check-input" type="checkbox" wire:model="removeImage">
                                    <span class="form-check-label text-danger fw-bold">{{ __('app.remove_image') }}</span>
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.position_system') }}</label>
                        <select class="form-select" wire:model="position_id">
                            <option value="">{{ __('app.choose_position') }}</option>
                            @php
                                $grouped = $positions->groupBy('category');
                            @endphp
                            @foreach($grouped as $category => $catPositions)
                                <optgroup label="{{ $category }}">
                                    @foreach($catPositions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.date_of_birth') }}</label>
                        <input type="text" class="form-control flatpickr-input" wire:model="date_of_birth" placeholder="{{ __('app.select_date') }}" data-date-format="Y-m-d" data-alt-format="d/m/Y">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.nationality') }}</label>
                        <input type="text" class="form-control" wire:model="nationality" placeholder="{{ __('app.nationality_placeholder') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('app.height') }} ({{ __('app.cm') }})</label>
                        <input type="number" class="form-control" wire:model="height" min="0" placeholder="175">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">{{ __('app.weight') }} ({{ __('app.kg') }})</label>
                        <input type="number" class="form-control" wire:model="weight" min="0" placeholder="70">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.foot') }}</label>
                        <select class="form-select" wire:model="foot">
                            <option value="">{{ __('app.choose') }}</option>
                            <option value="right">{{ __('app.right') }}</option>
                            <option value="left">{{ __('app.left') }}</option>
                            <option value="both">{{ __('app.both') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.sport_type') }}</label>
                        <select class="form-select" wire:model="sport_type" required>
                            <option value="football">{{ __('app.football') }}</option>
                            <option value="futsal">{{ __('app.futsal') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.player_bio') }}</label>
                        <textarea class="form-control" wire:model="bio" rows="3" placeholder="{{ __('app.bio_placeholder') }}"></textarea>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="is_captain" id="is_captain_edit">
                            <label class="form-check-label fw-bold" for="is_captain_edit">{{ __('app.team_captain') }}</label>
                        </div>
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
