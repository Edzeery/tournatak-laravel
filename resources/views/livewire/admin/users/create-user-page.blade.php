<div>
    <x-section-header
        :title="__('app.add_user').' '.__('app.new')"
        icon="bi-person-plus"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.users'), 'route' => route('admin.users.index')],
            ['label' => __('app.add_new')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-md">
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
                        <label class="form-label fw-bold">{{ __('app.full_name') }}</label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="{{ __('app.full_name') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.username') }}</label>
                        <input type="text" class="form-control" wire:model="username" required placeholder="username">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.email') }}</label>
                        <input type="email" class="form-control" wire:model="email" required placeholder="email@example.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.password') }}</label>
                        <input type="password" class="form-control" wire:model="password" required placeholder="••••••••">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.role') }}</label>
                        <select class="form-select" wire:model="role" required>
                            <option value="competitor">{{ __('app.competitor') }}</option>
                            <option value="captain">{{ __('app.captain') }}</option>
                            <option value="player">{{ __('app.player_role') }}</option>
                            <option value="organizer">{{ __('app.organizer') }}</option>
                            <option value="admin">{{ __('app.admin_role') }}</option>
                            <option value="user">{{ __('app.user_role') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.verified') }}</label>
                        <select class="form-select" wire:model="is_verified">
                            <option value="1">{{ __('app.verified') }}</option>
                            <option value="0">{{ __('app.unverified') }}</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_user') }}</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                </button>
            </form>
        </div>
    </div>
</div>
