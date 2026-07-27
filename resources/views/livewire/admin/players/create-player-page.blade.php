<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.players.index') }}" class="breadcrumb-link">{{ __('app.players') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.add_new') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-person-plus text-gold"></i> {{ __('app.add_player') }} {{ __('app.new') }}</h4>
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

            <form wire:submit="store">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.user') }}</label>
                        <select class="form-select" wire:model="user_id" required>
                            <option value="">{{ __('app.choose_user') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.team') }}</label>
                        <select class="form-select" wire:model="team_id" required>
                            <option value="">{{ __('app.choose_team') }}</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.player_number') }}</label>
                        <input type="number" class="form-control" wire:model="number" min="0" placeholder="10">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.position') }}</label>
                        <input type="text" class="form-control" wire:model="position_text" placeholder="{{ __('app.position_placeholder') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.image_url') }}</label>
                        <input type="text" class="form-control" wire:model="image" placeholder="https://...">
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
                            <input class="form-check-input" type="checkbox" wire:model="is_captain" id="is_captain">
                            <label class="form-check-label fw-bold" for="is_captain">{{ __('app.team_captain') }}</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_player') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
