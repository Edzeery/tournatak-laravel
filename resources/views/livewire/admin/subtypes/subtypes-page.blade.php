<div>
    <x-section-header
        :title="__('app.subtype_management')"
        :subtitle="__('app.subtypes_desc')"
        icon="bi-bookmark-fill"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.subtypes')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.subtypes.create') }}" class="btn btn-warning">
                <i class="bi bi-plus-lg"></i> {{ __('app.add_subtype') }}
            </a>
        </x-slot:action>
    </x-section-header>

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
                            <th>{{ __('app.name_ar') }}</th>
                            <th>{{ __('app.name_en') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subtypes as $subtype)
                            <tr wire:key="{{ $subtype->id }}">
                                <td class="text-chrome-muted">{{ $subtype->id }}</td>
                                <td class="fw-bold">{{ $subtype->name }}</td>
                                <td class="text-chrome-muted">{{ $subtype->en_name }}</td>
                                <td class="text-center d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.subtypes.edit', $subtype) }}" class="btn btn-sm btn-outline-primary rounded-md"
                                        aria-label="{{ __('app.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger rounded-md"
                                            wire:click="delete({{ $subtype->id }})"
                                            wire:confirm="{{ __('app.confirm_delete_subtype') }}"
                                            aria-label="{{ __('app.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="4">
                                    <x-empty-state icon="bi-tags" title="{{ __('app.subtypes') }}" message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $subtypes->links() }}</div>
</div>
