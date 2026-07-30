<div>
    <x-section-header
        :title="__('app.add_subtype').' '.__('app.new')"
        icon="bi-plus-circle"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.subtypes'), 'route' => route('admin.subtypes.index')],
            ['label' => __('app.add_new')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.subtypes.index') }}" class="btn btn-outline-secondary rounded-md">
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
                        <label class="form-label fw-bold">{{ __('app.name_arabic') }}</label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="{{ __('app.arabic_name_placeholder') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name_english') }}</label>
                        <input type="text" class="form-control" wire:model="en_name" required placeholder="English Name">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_subtype') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
