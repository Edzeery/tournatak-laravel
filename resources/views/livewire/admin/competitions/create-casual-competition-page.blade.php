<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.competitions.index') }}" class="breadcrumb-link">{{ __('app.competitions') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.create_casual') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-trophy-fill text-gold"></i> {{ __('app.page_title_create_casual_competition') }}</h4>
            <p class="text-chrome-muted mb-0 fs-sm">{{ __('app.casual_competition_desc') }}</p>
        </div>
        <a href="{{ route('admin.competitions.index') }}" class="btn btn-outline-secondary rounded-md">
            <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
        </a>
    </div>

    <div class="card border-0">
        <div class="card-body p-4">
            <form wire:submit="store">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="{{ __('app.enter_competition_name') }}">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.format') }} <span class="text-danger">*</span></label>
                        <select class="form-select" wire:model="format" required>
                            @foreach($formats as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('format') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.start_date') }}</label>
                        <input type="text" class="form-control flatpickr-input" wire:model="start_date" placeholder="{{ __('app.select_date_time') }}" data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.location') }}</label>
                        <input type="text" class="form-control" wire:model="location" placeholder="{{ __('app.city_or_stadium') }}">
                    </div>
                </div>

                <div class="alert alert-info d-flex align-items-center gap-2 mt-2 mb-3">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <span>{{ __('app.casual_auto_approved') }}</span>
                </div>

                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.create_casual') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
