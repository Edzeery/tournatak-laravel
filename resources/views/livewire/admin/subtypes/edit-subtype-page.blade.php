<div>
    <x-section-header
        :title="__('app.edit_subtype')"
        icon="bi-pencil"
        :subtitle="$subtype->name"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.subtypes'), 'route' => route('admin.subtypes.index')],
            ['label' => __('app.edit_subtype')],
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

            <form wire:submit="update">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name_arabic') }}</label>
                        <input type="text" class="form-control" wire:model="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name_english') }}</label>
                        <input type="text" class="form-control" wire:model="en_name" required>
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
