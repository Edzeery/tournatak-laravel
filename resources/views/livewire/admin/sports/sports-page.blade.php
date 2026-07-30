<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-trophy-fill text-gold"></i> {{ __('app.sport_management') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ __('app.sports_desc') }}</p>
        </div>
        <a href="{{ route('admin.sports.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_sport') }}
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.sports') }}</li>
        </ol>
    </nav>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_name_placeholder') }}" wire:model.live.debounce.300ms="search" aria-label="{{ __('app.search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold fs-base">{{ __('app.per_page_display') }}</label>
                    <select class="form-select" wire:model.live="perPage" aria-label="{{ __('app.per_page') }}">
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
                            <th>{{ __('app.name') }}</th>
                            <th>{{ __('app.slug') }}</th>
                            <th>{{ __('app.category') }}</th>
                            <th>{{ __('app.status') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sports as $sport)
                            <tr wire:key="{{ $sport->id }}">
                                <td class="text-chrome-muted">{{ $sport->id }}</td>
                                <td class="fw-bold">{{ $sport->name }}</td>
                                <td><code class="rounded-xs badge-type-gold">{{ $sport->slug }}</code></td>
                                <td>{{ $sport->category }}</td>
                                <td>
                                    @if($sport->is_active)
                                        <x-status-badge domain="competition" status="active" set="bi" />
                                    @else
                                        <x-status-badge domain="competition" status="inactive" set="bi" />
                                    @endif
                                </td>
                                <td class="text-center d-flex flex-wrap gap-2">
                                    <button class="btn btn-sm btn-outline-warning rounded-md" wire:click="toggleActive({{ $sport->id }})"
                                        aria-label="{{ __('app.toggle_active') }}">
                                        <i class="bi bi-toggle-{{ $sport->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                    <a href="{{ route('admin.sports.edit', $sport) }}" class="btn btn-sm btn-outline-primary rounded-md"
                                        aria-label="{{ __('app.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger rounded-md"
                                            wire:click="delete({{ $sport->id }})"
                                            wire:confirm="{{ __('app.confirm_delete_sport') }}"
                                            aria-label="{{ __('app.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="6">
                                    <x-empty-state icon="bi-trophy" title="{{ __('app.sports') }}" message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $sports->links() }}</div>
</div>
