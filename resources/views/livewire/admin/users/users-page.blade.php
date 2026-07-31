<div>
    <x-section-header
        icon="bi bi-people-fill"
        :title="__('app.user_management')"
        :subtitle="__('app.users_desc')"
        :breadcrumbs="[
            ['route' => route('admin.dashboard'), 'label' => __('app.dashboard')],
            ['label' => __('app.users')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.users.create') }}" class="btn btn-warning">
                <i class="bi bi-plus-lg"></i> {{ __('app.add_user') }}
            </a>
        </x-slot:action>
    </x-section-header>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_users_placeholder') }}"
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold fs-base">{{ __('app.role') }}</label>
                    <select class="form-select" wire:model.live="roleFilter">
                        <option value="">{{ __('app.all_roles') }}</option>
                        <option value="admin">{{ __('app.admin') }}</option>
                        <option value="organizer">{{ __('app.organizer') }}</option>
                        <option value="captain">{{ __('app.captain') }}</option>
                        <option value="player">{{ __('app.player') }}</option>
                        <option value="competitor">{{ __('app.role_competitor') }}</option>
                        <option value="user">{{ __('app.user') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold fs-base">{{ __('app.per_page_display') }}</label>
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div wire:loading>
        <x-skeleton type="table" :rows="5" />
    </div>

    <div class="card border-0" wire:loading.remove>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('app.user') }}</th>
                            <th>{{ __('app.email') }}</th>
                            <th>{{ __('app.role') }}</th>
                            <th>{{ __('app.verification') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr wire:key="{{ $user->id }}">
                                <td class="text-chrome-muted">{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold w-38 h-38 fs-base">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <div class="d-flex">
                                                <small class="ms-3 text-chrome-muted"> {{ __('attributes.username') }}:</small>
                                                <small class="ms-3 text-chrome-muted">{{ $user->username }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td><x-status-badge domain="role" status="{{ $user->role }}" set="bi" /></td>
                                <td>
                                    @if ($user->is_verified)
                                        <x-status-badge domain="user" status="active" set="bi" />
                                    @else
                                        <x-status-badge domain="user" status="email_unverified" set="bi" />
                                    @endif
                                </td>
                                <td class="text-center d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary rounded-md"
                                        aria-label="{{ __('app.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger rounded-md"
                                        wire:click="delete({{ $user->id }})"
                                        wire:confirm="{{ __('app.confirm_delete_user') }}"
                                        aria-label="{{ __('app.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="6">
                                    <x-empty-state icon="bi-people-fill" title="{{ __('app.users') }}" message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
</div>
