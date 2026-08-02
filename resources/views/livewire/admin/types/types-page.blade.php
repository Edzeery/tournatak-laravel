<div>
    <x-section-header
        icon="bi bi-tags-fill"
        :title="__('app.type_management')"
        :subtitle="__('app.types_desc')"
        :breadcrumbs="[
            ['route' => route('admin.dashboard'), 'label' => __('app.dashboard')],
            ['label' => __('app.types')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.types.create') }}" class="btn btn-warning">
                <i class="bi bi-plus-lg"></i> {{ __('app.add_type') }}
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
                            <th>{{ __('app.name') }}</th>
                            <th>{{ __('app.slug') }}</th>
                            <th>{{ __('app.category') }}</th>
                            <th>{{ __('app.participant_type') }}</th>
                            <th>{{ __('app.status') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                            <tr wire:key="{{ $type->id }}">
                                <td class="text-chrome-muted">{{ $type->id }}</td>
                                <td class="fw-bold">{{ $type->name }}</td>
                                <td><code class="rounded-xs badge-type-gold">{{ $type->slug }}</code></td>
                                <td>{{ $type->subtype->name ?? '-' }}</td>
                                <td>
                                    @if($type->participant_type === 'individual')
                                        <span class="badge bg-info">{{ __('app.participant_type_individual') }}</span>
                                    @elseif($type->participant_type === 'both')
                                        <span class="badge bg-warning">{{ __('app.participant_type_both') }}</span>
                                    @else
                                        <span class="badge bg-primary">{{ __('app.participant_type_team') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->is_active)
                                        <x-status-badge domain="competition" status="active" set="bi" />
                                    @else
                                        <x-status-badge domain="competition" status="inactive" set="bi" />
                                    @endif
                                </td>
                                <td class="text-center d-flex flex-wrap gap-2">
                                    <button class="btn btn-sm btn-outline-warning rounded-md" wire:click="toggleActive({{ $type->id }})"
                                        aria-label="{{ __('app.toggle_active') }}">
                                        <i class="bi bi-toggle-{{ $type->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                    <a href="{{ route('admin.types.edit', $type) }}" class="btn btn-sm btn-outline-primary rounded-md"
                                        aria-label="{{ __('app.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger rounded-md"
                                            x-on:click.prevent="confirmAction({ title: @js(__('app.confirm_delete_title')), text: @js(__('app.confirm_delete_type')), icon: 'warning', confirmButtonText: @js(__('app.confirm_delete_yes')), cancelButtonText: @js(__('app.confirm_delete_cancel')) }).then(ok => ok && $wire.delete({{ $type->id }}))"
                                            aria-label="{{ __('app.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="7">
                                    <x-empty-state icon="bi-tag" title="{{ __('app.types') }}" message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $types->links() }}</div>
</div>
