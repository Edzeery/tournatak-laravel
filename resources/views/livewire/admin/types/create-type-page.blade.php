<div>
    <x-section-header
        :title="__('app.add_type').' '.__('app.new')"
        icon="bi-plus-circle"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.types'), 'route' => route('admin.types.index')],
            ['label' => __('app.add_new')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.types.index') }}" class="btn btn-outline-secondary rounded-md">
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
                        <label class="form-label fw-bold">{{ __('app.type_name') }}</label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="{{ __('app.type_placeholder') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.slug') }}</label>
                        <input type="text" class="form-control bg-chrome" wire:model="slug" placeholder="{{ __('app.auto_generated') }}" readonly>
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
                        <label class="form-label fw-bold">{{ __('app.participant_type') }}</label>
                        <select class="form-select" wire:model="participant_type" required>
                            <option value="team">{{ __('app.participant_type_team') }}</option>
                            <option value="individual">{{ __('app.participant_type_individual') }}</option>
                            <option value="both">{{ __('app.participant_type_both') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.icon') }}</label>
                        <input type="text" class="form-control" wire:model="icon" placeholder="bi-trophy">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.sort_order') }}</label>
                        <input type="number" class="form-control" wire:model="sort_order" min="0" value="0">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">{{ __('app.description') }}</label>
                        <textarea class="form-control" wire:model="description" rows="2" placeholder="{{ __('app.description_placeholder') }}"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_type') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
