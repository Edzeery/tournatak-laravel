<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary">
                <i class="bi bi-person-plus-fill text-gold"></i> {{ __('app.registration_management') }}
            </h4>
            <p class="text-muted mb-0 fs-md">{{ __('app.registrations_desc') }}</p>
        </div>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a>
            </li>
            <li class="breadcrumb-item active">{{ __('app.registrations') }}</li>
        </ol>
    </nav>

    {{-- Filters --}}
    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control"
                        placeholder="{{ __('app.search_registrations_placeholder') }}"
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold fs-base">{{ __('app.participant_type') }}</label>
                    <select class="form-select" wire:model.live="participantTypeFilter">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="team">{{ __('app.participant_type_team') }}</option>
                        <option value="individual">{{ __('app.participant_type_individual') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold fs-base">{{ __('app.status') }}</label>
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="pending">{{ __('app.pending') }}</option>
                        <option value="approved">{{ __('app.approved') }}</option>
                        <option value="rejected">{{ __('app.rejected') }}</option>
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

    {{-- Loading skeleton --}}
    <div wire:loading>
        <x-skeleton type="table" :rows="5" />
    </div>

    {{-- Table --}}
    <div class="card border-0" wire:loading.remove>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('app.competition') }}</th>
                            <th>{{ __('app.participant_type') }}</th>
                            <th>{{ __('app.participant') }}</th>
                            <th>{{ __('app.status') }}</th>
                            <th>{{ __('app.registration_date') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr wire:key="{{ $registration->id }}">
                                <td class="text-chrome-muted">{{ $registration->id }}</td>
                                <td class="fw-bold">{{ $registration->competition?->name ?? '-' }}</td>
                                <td>
                                    @if($registration->participant_type === 'individual')
                                        <span class="badge bg-info">{{ __('app.participant_type_individual') }}</span>
                                    @else
                                        <span class="badge bg-primary">{{ __('app.participant_type_team') }}</span>
                                    @endif
                                </td>
                                <td>{{ $registration->getParticipantName() ?? '-' }}</td>
                                <td>
                                    <x-status-badge domain="general" class="bg-success-subtle text-success " status="{{ $registration->status }}" set="bi" />

                                </td>
                                <td class="text-chrome-muted">{{ $registration->created_at->format('Y-m-d H:i') }}</td>
                                <td class="text-center d-flex flex-wrap gap-2 justify-content-center">
                                    @if($registration->status === 'pending')
                                        <button class="btn btn-sm btn-outline-success rounded-md"
                                            wire:click="approve({{ $registration->id }})"
                                            wire:confirm="{{ __('app.confirm_approve_registration') }}">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-md"
                                            wire:click="reject({{ $registration->id }})"
                                            wire:confirm="{{ __('app.confirm_reject_registration') }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-danger rounded-md"
                                        wire:click="delete({{ $registration->id }})"
                                        wire:confirm="{{ __('app.confirm_delete_registration') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="7">
                                    <x-empty-state icon="bi-person-plus" title="{{ __('app.registrations') }}"
                                        message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $registrations->links() }}</div>
</div>
