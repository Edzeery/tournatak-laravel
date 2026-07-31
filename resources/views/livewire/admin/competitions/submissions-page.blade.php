<div>
    <x-section-header
        icon="bi-clipboard-check"
        :title="__('app.manage_submissions')"
        :subtitle="$competition->name"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.competitions'), 'route' => route('admin.competitions.index')],
            ['label' => $competition->name, 'route' => route('admin.competitions.edit', $competition)],
            ['label' => __('app.manage_submissions')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.competitions.edit', $competition) }}" class="btn btn-outline-secondary rounded-md">
                <i class="bi bi-arrow-right"></i> {{ __('app.back_button') }}
            </a>
        </x-slot:action>
    </x-section-header>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0">
                <div class="card-header bg-transparent py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-1"></i> {{ __('app.all_submissions') }}</h6>
                    <select class="form-select form-select-sm w-auto" wire:model.live="round_id">
                        <option value="">{{ __('app.all_rounds') }}</option>
                        @foreach($rounds as $round)
                            <option value="{{ $round->id }}">{{ __('app.round', ['number' => $round->number]) }} — {{ $round->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('app.participant') }}</th>
                                    <th>{{ __('app.submission_title') }}</th>
                                    <th>{{ __('app.submission_round') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th class="text-center">{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submissions as $submission)
                                    <tr wire:key="submission-{{ $submission->id }}">
                                        <td class="text-chrome-muted">{{ $submission->id }}</td>
                                        <td class="fw-bold">{{ $submission->getParticipantName() ?? '—' }}</td>
                                        <td>
                                            @if ($editSubmissionId === $submission->id)
                                                <input type="text" class="form-control form-control-sm" wire:model="editTitle">
                                                <textarea class="form-control form-control-sm mt-1" rows="2" wire:model="editDescription" placeholder="{{ __('app.submission_description') }}"></textarea>
                                                @error('editTitle') <div class="text-danger fs-sm mt-1">{{ $message }}</div> @enderror
                                            @else
                                                <div class="fw-bold">{{ $submission->title }}</div>
                                                @if ($submission->description)
                                                    <div class="text-chrome-muted fs-sm">{{ Str::limit($submission->description, 60) }}</div>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($editSubmissionId === $submission->id)
                                                <select class="form-select form-select-sm" wire:model="editRoundId">
                                                    @foreach($rounds as $round)
                                                        <option value="{{ $round->id }}">{{ __('app.round', ['number' => $round->number]) }} — {{ $round->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <span class="badge badge-sport">{{ $submission->round?->number ? __('app.round', ['number' => $submission->round->number]) : '—' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($editSubmissionId === $submission->id)
                                                <select class="form-select form-select-sm" wire:model="editStatus">
                                                    @foreach($statuses as $status)
                                                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <x-status-badge domain="general" :status="$submission->status->value" set="bi" />
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($editSubmissionId === $submission->id)
                                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                                    <button class="btn btn-sm btn-warning rounded-md" wire:click="update">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-secondary rounded-md" wire:click="cancelEdit">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                                    <button class="btn btn-sm btn-outline-primary rounded-md" wire:click="startEdit({{ $submission->id }})" aria-label="{{ __('app.edit') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <select class="form-select form-select-sm w-auto" wire:change="setStatus({{ $submission->id }}, $event.target.value)" aria-label="{{ __('app.status') }}">
                                                        @foreach($statuses as $status)
                                                            <option value="{{ $status->value }}" @selected($submission->status === $status)>{{ $status->label() }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <x-empty-state icon="bi-clipboard-check" title="{{ __('app.no_submissions_yet') }}" message="{{ __('app.no_submissions_yet_message') }}" />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-3">{{ $submissions->links() }}</div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-plus-lg me-1"></i> {{ __('app.add_submission') }}</h6>
                </div>
                <div class="card-body p-4">
                    <form wire:submit="create">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.submission_round') }}</label>
                            <select class="form-select" wire:model="newRoundId" required>
                                @foreach($rounds as $round)
                                    <option value="{{ $round->id }}">{{ __('app.round', ['number' => $round->number]) }} — {{ $round->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.submission_title') }}</label>
                            <input type="text" class="form-control" wire:model="newTitle" required>
                            @error('newTitle') <div class="text-danger fs-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.submission_description') }}</label>
                            <textarea class="form-control" rows="3" wire:model="newDescription"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.participant') }}</label>
                            @if ($competition->domain && $competition->domain->participant_basis === 'individual')
                                <input type="hidden" wire:model="newParticipantType" value="individual">
                            @elseif ($competition->domain && $competition->domain->participant_basis === 'team')
                                <input type="hidden" wire:model="newParticipantType" value="team">
                            @else
                                <select class="form-select mb-2" wire:model.live="newParticipantType">
                                    <option value="team">{{ __('app.participant_type_team') }}</option>
                                    <option value="individual">{{ __('app.participant_type_individual') }}</option>
                                </select>
                            @endif

                            @if ($newParticipantType === 'team')
                                <select class="form-select" wire:model="newParticipantId" required>
                                    <option value="">—</option>
                                    @foreach($teamOptions as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select class="form-select" wire:model="newParticipantId" required>
                                    <option value="">—</option>
                                    @foreach($individualOptions as $registration)
                                        <option value="{{ $registration->user_id }}">{{ $registration->user?->name ?? '—' }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('newParticipantId') <div class="text-danger fs-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-warning w-100" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="create"><i class="bi bi-check-lg"></i> {{ __('app.add_submission') }}</span>
                            <span wire:loading wire:target="create"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
