<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-trophy-fill text-gold"></i> {{ __('app.manage_competitions') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ __('app.competitions_desc') }}</p>
        </div>
        <a href="{{ route('admin.competitions.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_competition') }}
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.competitions') }}</li>
        </ol>
    </nav>

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
                            <th>{{ __('app.type') }}</th>
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
                                    <x-status-badge domain="competition" status="{{ $competition->status }}" set="bi" />
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
                                <td class="text-center">
                                    <a href="{{ route('admin.competitions.edit', $competition) }}" class="btn btn-sm btn-outline-primary rounded-md"
                                        aria-label="{{ __('app.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
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
                                <td colspan="8">
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
