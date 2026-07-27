<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="breadcrumb-link">{{ __('app.users') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.add_new') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-person-plus text-gold"></i> {{ __('app.add_user') }} {{ __('app.new') }}</h4>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-md">
            <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
        </a>
    </div>

    <div class="card border-0">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                            <option value="viewer">{{ __('app.viewer') }}</option>
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
