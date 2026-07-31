<div>
    <x-section-header
        :title="__('app.add_player').' '.__('app.new')"
        icon="bi-person-plus"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.players'), 'route' => route('admin.players.index')],
            ['label' => __('app.add_new')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.players.index') }}" class="btn btn-outline-secondary rounded-md">
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
