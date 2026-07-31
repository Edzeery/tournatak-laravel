<div>
    <x-section-header
        :title="__('app.edit_sport')"
        icon="bi-pencil"
        :subtitle="$sport->name"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.sports'), 'route' => route('admin.sports.index')],
            ['label' => __('app.edit_sport')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.sports.index') }}" class="btn btn-outline-secondary rounded-md">
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
                        <label class="form-label fw-bold">{{ __('app.name') }}</label>
                        <input type="text" class="form-control" wire:model="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.slug') }}</label>
                        <input type="text" class="form-control bg-chrome" wire:model="slug" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name_en') }}</label>
                        <input type="text" class="form-control" wire:model="name_en">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name_fr') }}</label>
                        <input type="text" class="form-control" wire:model="name_fr">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name_es') }}</label>
                        <input type="text" class="form-control" wire:model="name_es">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.category') }}</label>
                        <select class="form-select" wire:model="category" required>
                            <option value="team" {{ $sport->category === 'team' ? 'selected' : '' }}>{{ __('app.category_team') }}</option>
                            <option value="individual" {{ $sport->category === 'individual' ? 'selected' : '' }}>{{ __('app.category_individual') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.icon') }}</label>
                        <input type="text" class="form-control" wire:model="icon">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.sort_order') }}</label>
                        <input type="number" class="form-control" wire:model="sort_order" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.status') }}</label>
                        <select class="form-select" wire:model="is_active">
                            <option value="1" {{ $sport->is_active ? 'selected' : '' }}>{{ __('app.active') }}</option>
                            <option value="0" {{ !$sport->is_active ? 'selected' : '' }}>{{ __('app.inactive') }}</option>
                        </select>
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
