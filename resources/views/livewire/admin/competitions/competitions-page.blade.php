<div>
    <x-section-header
        icon="bi bi-trophy-fill"
        :title="__('app.manage_competitions')"
        :subtitle="__('app.competitions_desc')"
        :breadcrumbs="[
            ['route' => route('admin.dashboard'), 'label' => __('app.dashboard')],
            ['label' => __('app.competitions')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.competitions.create') }}" class="btn btn-warning">
                <i class="bi bi-plus-lg"></i> {{ __('app.add_competition') }}
            </a>
        </x-slot:action>
    </x-section-header>

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
                            <th>{{ __('app.competition') }}</th>
                            <th>{{ __('app.domain') }}</th>
                            <th>{{ __('app.type') }}</th>
                            <th>{{ __('app.participant_type') }}</th>
                            <th>{{ __('app.organizer') }}</th>
                            <th>{{ __('app.dates') }}</th>
                            <th>{{ __('app.status') }}</th>
                            <th>{{ __('app.approval') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($competitions as $competition)
                            <tr wire:key="{{ $competition->id }}">
                                <td class="text-chrome-muted">{{ $competition->id }}</td>
                                <td class="fw-bold">{{ $competition->name }}</td>
                                <td>
                                    @if ($competition->domain)
                                        <span class="badge badge-domain">
                                            <i class="bi {{ $competition->domain->icon }} me-1"></i>
                                            {{ $competition->domain->localizedName() }}
                                        </span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td>{{ $competition->type?->name ?? '-' }}</td>
                                <td>
                                    @if($competition->type?->participant_type === 'individual')
                                        <span class="badge bg-info">{{ __('app.participant_type_individual') }}</span>
                                    @elseif($competition->type?->participant_type === 'both')
                                        <span class="badge bg-warning">{{ __('app.participant_type_both') }}</span>
                                    @else
                                        <span class="badge bg-primary">{{ __('app.participant_type_team') }}</span>
                                    @endif
                                </td>
                                <td>{{ $competition->organizer->name ?? '-' }}</td>
                                <td class="fs-base">
                                    {{ formatDate($competition->start_date) ?? '-' }}
                                    <i class="bi bi-arrow-left text-muted mx-1"></i>
                                    {{ formatDate($competition->end_date) ?? '-' }}
                                </td>
                                <td>
                                    <x-status-badge domain="competition" status="{{ $competition->status }}" set="bi" />
                                </td>
                                <td>
                                    <x-status-badge domain="competition" status="{{ $competition->approval_status }}" set="bi" />
                                </td>
                                <td class="text-center d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.competitions.edit', $competition) }}" class="btn btn-sm btn-outline-primary rounded-md"
                                        aria-label="{{ __('app.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($competition->usesSubmissionEvaluation())
                                        <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('app.manage_submissions') }}">
                                            <a href="{{ route('admin.competitions.rounds', $competition) }}" class="btn btn-outline-secondary rounded-md" title="{{ __('app.manage_rounds') }}">
                                                <i class="bi bi-layers"></i>
                                            </a>
                                            <a href="{{ route('admin.competitions.submissions', $competition) }}" class="btn btn-outline-secondary rounded-md" title="{{ __('app.manage_submissions') }}">
                                                <i class="bi bi-clipboard-check"></i>
                                            </a>
                                            <a href="{{ route('admin.competitions.judging', $competition) }}" class="btn btn-outline-secondary rounded-md" title="{{ __('app.manage_judging') }}">
                                                <i class="bi bi-people"></i>
                                            </a>
                                        </div>
                                    @endif
                                    @if($competition->approval_status === 'pending')
                                        <button class="btn btn-sm btn-outline-success rounded-md" wire:click="approve({{ $competition->id }})"
                                            aria-label="{{ __('app.approve') }}">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-md" wire:click="reject({{ $competition->id }})"
                                            aria-label="{{ __('app.reject') }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="10">
                                    <x-empty-state icon="bi-trophy" title="{{ __('app.no_competitions_found') }}" message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $competitions->links() }}</div>
</div>
