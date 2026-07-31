<div>
    <x-section-header
        :title="__('app.add_sport').' '.__('app.new')"
        icon="bi-plus-circle"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.sports'), 'route' => route('admin.sports.index')],
            ['label' => __('app.add_new')],
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

            <form wire:submit="store">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name') }}</label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="{{ __('app.sport_name_placeholder') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.slug') }}</label>
                        <input type="text" class="form-control bg-chrome" wire:model="slug" placeholder="{{ __('app.auto_generated') }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name_en') }}</label>
                        <input type="text" class="form-control" wire:model="name_en" placeholder="English name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name_fr') }}</label>
                        <input type="text" class="form-control" wire:model="name_fr" placeholder="French name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name_es') }}</label>
                        <input type="text" class="form-control" wire:model="name_es" placeholder="Spanish name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.category') }}</label>
                        <select class="form-select" wire:model="category" required>
                            <option value="team">{{ __('app.category_team') }}</option>
                            <option value="individual">{{ __('app.category_individual') }}</option>
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
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_sport') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
