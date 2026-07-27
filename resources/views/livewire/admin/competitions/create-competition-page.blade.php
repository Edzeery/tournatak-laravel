<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.competitions.index') }}" class="breadcrumb-link">{{ __('app.competitions') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.add_new') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-trophy-fill text-gold"></i> {{ __('app.add_new_competition') }}</h4>
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
                        <label class="form-label fw-bold">{{ __('app.name') }}</label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="{{ __('app.enter_competition_name') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.type') }}</label>
                        <select class="form-select" wire:model="type_id" required>
                            <option value="">{{ __('app.choose_type') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.subtype') }}</label>
                        <select class="form-select" wire:model="subtype_id" required>
                            <option value="">{{ __('app.choose_subtype') }}</option>
                            @foreach($subtypes as $subtype)
                                <option value="{{ $subtype->id }}">{{ $subtype->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.location') }}</label>
                        <input type="text" class="form-control" wire:model="location" placeholder="{{ __('app.city_or_stadium') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.start_date') }}</label>
                        <input type="text" class="form-control flatpickr-input" wire:model="start_date" placeholder="{{ __('app.select_date_time') }}" data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.end_date') }}</label>
                        <input type="text" class="form-control flatpickr-input" wire:model="end_date" placeholder="{{ __('app.select_date_time') }}" data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">{{ __('app.description') }}</label>
                        <textarea class="form-control" wire:model="description" rows="3" placeholder="{{ __('app.competition_desc_placeholder') }}"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_competition') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
