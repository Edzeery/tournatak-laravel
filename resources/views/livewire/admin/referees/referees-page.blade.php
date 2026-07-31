<div>
    <x-section-header
        icon="bi bi-people-fill"
        :title="__('app.referees')"
        :subtitle="__('app.referees_desc')"
        :breadcrumbs="[
            ['route' => route('admin.dashboard'), 'label' => __('app.dashboard')],
            ['label' => __('app.referees')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.referees.create') }}" class="btn btn-warning">
                <i class="bi bi-plus-lg"></i> {{ __('app.add_referee') }}
            </a>
        </x-slot:action>
    </x-section-header>

    {{-- Filters --}}
    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_referee_placeholder') }}"
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold fs-base">{{ __('app.specialization') }}</label>
                    <select class="form-select" wire:model.live="specializationFilter">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="referee">{{ __('app.main_referee') }}</option>
                        <option value="assistant_referee">{{ __('app.assistant_referee') }}</option>
                        <option value="fourth_official">{{ __('app.fourth_official') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold fs-base">{{ __('app.per_page') }}</label>
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-chrome-muted fs-xs" style="width:50px;">#</th>
                            <th class="fs-xs text-uppercase text-chrome-muted">{{ __('app.name') }}</th>
                            <th class="fs-xs text-uppercase text-chrome-muted">{{ __('app.specialization') }}</th>
                            <th class="fs-xs text-uppercase text-chrome-muted">{{ __('app.contact') }}</th>
                            <th class="fs-xs text-uppercase text-chrome-muted">{{ __('app.federation') }}</th>
                            <th class="text-center fs-xs text-uppercase text-chrome-muted" style="width:80px;">{{ __('app.status') }}</th>
                            <th class="text-center fs-xs text-uppercase text-chrome-muted" style="width:120px;">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referees as $referee)
                            <tr wire:key="ref-{{ $referee->id }}">
                                <td class="text-chrome-muted fs-sm">{{ $referee->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($referee->photo)
                                            <img src="{{ $referee->photo }}" alt="" class="rounded-circle object-cover flex-shrink-0" style="width:32px;height:32px;">
                                        @else
                                            <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:32px;height:32px;font-size:0.85rem;">
                                                {{ mb_substr($referee->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold fs-sm">{{ $referee->name }}</div>
                                            @if($referee->license_number)
                                                <div class="text-chrome-muted fs-xs">{{ __('app.license') }}: {{ $referee->license_number }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $referee->specialization === 'referee' ? 'danger' : ($referee->specialization === 'assistant_referee' ? 'info' : 'secondary') }} bg-opacity-10 text-{{ $referee->specialization === 'referee' ? 'danger' : ($referee->specialization === 'assistant_referee' ? 'info' : 'secondary') }} rounded-pill fs-xs px-2">
                                        {{ __("app.spec_{$referee->specialization}") }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fs-sm">
                                        @if($referee->email)
                                            <div><i class="bi bi-envelope text-chrome-muted me-1"></i>{{ $referee->email }}</div>
                                        @endif
                                        @if($referee->phone)
                                            <div><i class="bi bi-telephone text-chrome-muted me-1"></i>{{ $referee->phone }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="fs-sm">{{ $referee->federation ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $referee->is_active ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $referee->is_active ? 'success' : 'secondary' }} rounded-pill">
                                        {{ $referee->is_active ? __('app.active') : __('app.inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.referees.edit', $referee) }}"
                                            class="btn btn-sm btn-outline-primary rounded-md" title="{{ __('app.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-{{ $referee->is_active ? 'warning' : 'success' }} rounded-md"
                                            wire:click="toggleActive({{ $referee->id }})"
                                            title="{{ $referee->is_active ? __('app.deactivate') : __('app.activate') }}">
                                            <i class="bi bi-{{ $referee->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <x-empty-state icon="bi-people" title="{{ __('app.referees') }}"
                                        message="{{ __('app.no_referees_yet') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $referees->links() }}</div>
</div>
