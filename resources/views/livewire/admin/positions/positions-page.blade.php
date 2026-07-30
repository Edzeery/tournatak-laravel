<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.positions') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark-theme"><i class="bi bi-geo-alt-fill text-gold"></i> {{ __('app.position_management') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ __('app.positions_desc') }}</p>
        </div>
        <button class="btn btn-warning" wire:click="openModal">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_position') }}
        </button>
    </div>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_positions_placeholder') }}" wire:model.live.debounce.300ms="search" aria-label="{{ __('app.search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold fs-base">{{ __('app.sport') }}</label>
                    <select class="form-select" wire:model.live="filterSport" aria-label="{{ __('app.sport') }}">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}">{{ $sport->localizedName() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div wire:loading>
        <x-skeleton type="table" :rows="5" />
    </div>

    <div class="card border-0" wire:loading.remove>
        <div class="card-body">
            @if($positions->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="fs-08">#</th>
                                <th class="fs-08">{{ __('app.name') }}</th>
                                <th class="fs-08">{{ __('app.name_en') }}</th>
                                <th class="fs-08">{{ __('app.abbreviation') }}</th>
                                <th class="fs-08">{{ __('app.category') }}</th>
                                <th class="fs-08">{{ __('app.sport') }}</th>
                                <th class="fs-08">{{ __('app.sort_order') }}</th>
                                <th class="fs-08">{{ __('app.status') }}</th>
                                <th class="fs-08">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positions as $pos)
                                <tr wire:key="{{ $pos->id }}">
                                    <td class="fs-base">{{ $loop->iteration }}</td>
                                    <td class="fw-bold fs-md">{{ $pos->name }}</td>
                                    <td class="fs-base text-slate-500">{{ $pos->name_en ?? '—' }}</td>
                                    <td><span class="badge bg-dark rounded-pill fs-sm">{{ $pos->abbreviation ?? '—' }}</span></td>
                                    <td class="fs-base">
                                        @php
                                            $catColors = ['goalkeeper' => '#f59e0b', 'defender' => '#3b82f6', 'midfielder' => '#10b981', 'forward' => '#ef4444', 'player' => '#64748b'];
                                        @endphp
                                        <span style="font-size:0.75rem;padding:3px 8px;border-radius:6px;background:{{ $catColors[$pos->category] ?? '#64748b' }}20;color:{{ $catColors[$pos->category] ?? '#64748b' }};">
                                            {{ __("app.{$pos->category}", [], app()->getLocale()) !== "app.{$pos->category}" ? __("app.{$pos->category}") : $pos->category }}
                                        </span>
                                    </td>
                                    <td class="fs-base">{{ $pos->sport?->localizedName() ?? __('app.football') }}</td>
                                    <td class="fs-base">{{ $pos->sort_order }}</td>
                                    <td>
                                        @if($pos->is_active)
                                            <span class="badge badge-active-green fs-sm">{{ __('app.active_label') }}</span>
                                        @else
                                            <span class="badge bg-secondary fs-sm">{{ __('app.inactive_label') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-outline-primary rounded-md" wire:click="editPosition({{ $pos->id }})"
                                                aria-label="{{ __('app.edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger rounded-md"
                                                    wire:click="deletePosition({{ $pos->id }})"
                                                    wire:confirm="{{ __('app.confirm_delete_position') }}"
                                                    aria-label="{{ __('app.delete') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5" wire:loading.remove>
                    <x-empty-state icon="bi-geo-alt" title="{{ __('app.positions') }}" message="{{ __('app.no_results_found') }}" />
                </div>
            @endif
        </div>
    </div>

    @if($showModal)
        <div class="modal fade show d-block modal-backdrop-dark" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="positionModalTitle" wire:click.self="closeModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-xl" wire:click.stop>
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="positionModalTitle">
                            <i class="bi bi-geo-alt-fill text-gold"></i>
                            {{ $editingPositionId ? __('app.edit_position') : __('app.add_new_position') }}
                        </h5>
                        <button type="button" class="btn-close" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.sport') }}</label>
                            <select class="form-select" wire:model.live="positionForm.sport_id">
                                <option value="">{{ __('app.choose_sport') }}</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}">{{ $sport->localizedName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.position_name_ar') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('app.position_name_ar') }}" wire:model="positionForm.name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.position_name_en') }}</label>
                            <input type="text" class="form-control" placeholder="e.g. Goalkeeper" wire:model="positionForm.name_en">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.abbreviation') }}</label>
                                <input type="text" class="form-control" placeholder="e.g. GK" wire:model="positionForm.abbreviation" maxlength="10">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.sort_order') }}</label>
                                <input type="number" class="form-control" wire:model="positionForm.sort_order" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.category') }}</label>
                                <select class="form-select" wire:model="positionForm.category">
                                    @foreach($sportCategories as $cat)
                                        <option value="{{ $cat }}">{{ __("app.{$cat}") }}</option>
                                    @endforeach
                                    <option value="player">{{ __('app.player_fallback') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model="positionForm.is_active" id="posActive">
                                <label class="form-check-label fw-bold" for="posActive">{{ __('app.active_label') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary rounded-md" wire:click="closeModal">{{ __('app.confirm_delete_cancel') }}</button>
                        <button type="button" class="btn btn-warning px-4 rounded-md" wire:click="savePosition" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="savePosition"><i class="bi bi-check-lg"></i> {{ __('app.save_button') }}</span>
                            <span wire:loading wire:target="savePosition"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
