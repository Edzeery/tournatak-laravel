<div>
    <x-section-header
        :title="__('app.add_referee')"
        icon="bi-plus-circle"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.referees'), 'route' => route('admin.referees.index')],
            ['label' => __('app.add_referee')],
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

            <form wire:submit="store">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="{{ __('app.referee_name_placeholder') }}">
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
                        <input type="email" class="form-control" wire:model="email" placeholder="referee@example.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.phone') }}</label>
                        <input type="tel" class="form-control" wire:model="phone" placeholder="+213 xxx xx xx xx">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.license_number') }}</label>
                        <input type="text" class="form-control" wire:model="license_number" placeholder="{{ __('app.license_placeholder') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.federation') }}</label>
                        <input type="text" class="form-control" wire:model="federation" placeholder="{{ __('app.federation_placeholder') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.nationality') }}</label>
                        <input type="text" class="form-control" wire:model="nationality" placeholder="{{ __('app.nationality_placeholder') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="isActive" wire:model="is_active" checked>
                            <label class="form-check-label fw-bold" for="isActive">{{ __('app.active') }}</label>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">{{ __('app.notes') }}</label>
                        <textarea class="form-control" wire:model="notes" rows="3" placeholder="{{ __('app.referee_notes_placeholder') }}"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_referee') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
