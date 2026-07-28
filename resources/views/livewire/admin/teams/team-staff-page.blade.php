<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="breadcrumb-link">{{ __('app.teams') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.edit', $team) }}" class="breadcrumb-link">{{ $team->name }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.staff') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark-theme"><i class="bi bi-people-fill text-gold"></i> {{ __('app.technical_staff') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ $team->name }}</p>
        </div>
        <button class="btn btn-warning" wire:click="openModal">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_staff_member') }}
        </button>
    </div>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_member_by_name') }}" wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3" wire:loading.opacity>
        @forelse($staff as $member)
            <div class="col-md-4 col-lg-3" wire:key="{{ $member->id }}">
                <div class="card border-0 h-100 rounded-lg-custom shadow-sm">
                    <div class="card-body text-center p-3">
                        <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle bg-light w-56 h-56">
                            <i class="bi {{ $staffIcons[$member->staff_role] ?? 'bi-person' }} text-gold fs-xl"></i>
                        </div>
                        <h6 class="fw-bold mb-1 fs-md">{{ $member->user->name ?? '—' }}</h6>
                        <span class="badge bg-warning-subtle text-warning fw-bold mb-2 fs-sm">
                            {{ $staffRoles[$member->staff_role] ?? $member->staff_role }}
                        </span>
                        @if($member->specialization)
                            <p class="text-muted mb-2 fs-08">
                                <i class="bi bi-tag"></i> {{ $member->specialization }}
                            </p>
                        @endif
                        <div class="text-muted mb-2 fs-sm">
                            @if($member->start_date)
                                <div><i class="bi bi-calendar-event"></i> {{ formatDate($member->start_date) }}</div>
                            @endif
                            @if($member->end_date)
                                <div><i class="bi bi-calendar-check"></i> {{ formatDate($member->end_date) }}</div>
                            @endif
                        </div>
                        <div class="d-flex gap-1 justify-content-center mt-auto pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary rounded-md" wire:click="editStaff({{ $member->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger rounded-md"
                                    wire:click="deleteStaff({{ $member->id }})"
                                    wire:confirm="{{ __('app.confirm_delete_staff') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-body py-5 text-center">
                        <div class="empty-state py-3">
                            <i class="bi bi-people d-block fs-4xl"></i>
                            <h5>{{ __('app.no_staff') }}</h5>
                            <p class="text-muted">{{ __('app.no_staff_desc') }}</p>
                            <button class="btn btn-warning" wire:click="openModal">
                                <i class="bi bi-plus-lg"></i> {{ __('app.add_staff_member') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($showModal)
        <div class="modal fade show d-block modal-overlay-blur" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="staffModalTitle" wire:click.self="closeModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-xl" wire:click.stop>
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="staffModalTitle">
                            <i class="bi bi-people-fill text-gold"></i>
                            {{ $editingStaffId ? __('app.edit_staff_member') : __('app.add_staff_member') }}
                        </h5>
                        <button type="button" class="btn-close" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.search_user') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('app.search_user_placeholder') }}"
                                   wire:model.live.debounce.300ms="userSearch">
                            @if(count($searchedUsers) > 0)
                                <div class="list-group mt-1" style="max-height:200px;overflow-y:auto;z-index:10;">
                                    @foreach($searchedUsers as $u)
                                        <button type="button" class="list-group-item list-group-item-action"
                                                wire:click="selectUser({{ $u->id }})">
                                            {{ $u->name }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.staff_role') }}</label>
                            <select class="form-select" wire:model="staffForm.staff_role">
                                <option value="">{{ __('app.choose_role') }}</option>
                                @foreach($staffRoles as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.specialization') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('app.specialization_placeholder') }}" wire:model="staffForm.specialization">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.start_date') }}</label>
                                <input type="text" class="form-control flatpickr-input" wire:model="staffForm.start_date" placeholder="{{ __('app.select_date') }}" data-date-format="Y-m-d" data-alt-format="d/m/Y">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.end_date') }}</label>
                                <input type="text" class="form-control flatpickr-input" wire:model="staffForm.end_date" placeholder="{{ __('app.select_date') }}" data-date-format="Y-m-d" data-alt-format="d/m/Y">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary rounded-md" wire:click="closeModal">{{ __('app.cancel') }}</button>
                        <button type="button" class="btn btn-warning px-4 rounded-md" wire:click="saveStaff" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveStaff"><i class="bi bi-check-lg"></i> {{ __('app.save') }}</span>
                            <span wire:loading wire:target="saveStaff"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
