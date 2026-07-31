<div>
    <x-section-header
        :title="__('app.edit_referee')"
        icon="bi-pencil"
        :subtitle="$referee->name"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.referees'), 'route' => route('admin.referees.index')],
            ['label' => __('app.edit_referee')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.referees.index') }}" class="btn btn-outline-secondary rounded-md">
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
                        <label class="form-label fw-bold">{{ __('app.name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.specialization') }}</label>
                        <select class="form-select" wire:model="specialization">
                            <option value="referee">{{ __('app.main_referee') }}</option>
                            <option value="assistant_referee">{{ __('app.assistant_referee') }}</option>
                            <option value="fourth_official">{{ __('app.fourth_official') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.email') }}</label>
                        <input type="email" class="form-control" wire:model="email">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.phone') }}</label>
                        <input type="tel" class="form-control" wire:model="phone">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.license_number') }}</label>
                        <input type="text" class="form-control" wire:model="license_number">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.federation') }}</label>
                        <input type="text" class="form-control" wire:model="federation">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.nationality') }}</label>
                        <input type="text" class="form-control" wire:model="nationality">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="isActive" wire:model="is_active">
                            <label class="form-check-label fw-bold" for="isActive">{{ __('app.active') }}</label>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">{{ __('app.notes') }}</label>
                        <textarea class="form-control" wire:model="notes" rows="3"></textarea>
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
