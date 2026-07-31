<div>
    <x-section-header
        icon="bi-layers"
        :title="__('app.manage_rounds')"
        :subtitle="$competition->name"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.competitions'), 'route' => route('admin.competitions.index')],
            ['label' => $competition->name, 'route' => route('admin.competitions.edit', $competition)],
            ['label' => __('app.manage_rounds')],
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
        <div class="col-lg-7">
            <div class="card border-0">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-list-ol me-1"></i> {{ __('app.rounds') }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('app.name') }}</th>
                                    <th class="text-center">{{ __('app.submissions') }}</th>
                                    <th class="text-center">{{ __('app.round_starts') }}</th>
                                    <th class="text-center">{{ __('app.round_ends') }}</th>
                                    <th class="text-center">{{ __('app.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rounds as $round)
                                    <tr wire:key="round-{{ $round->id }}">
                                        <td class="fw-bold text-chrome-muted">{{ $round->number }}</td>
                                        <td class="fw-bold">{{ $round->name }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.competitions.submissions', ['competition' => $competition, 'round' => $round->id]) }}" class="badge badge-sport text-decoration-none">
                                                {{ $round->submissions_count }}
                                            </a>
                                        </td>
                                        <td class="text-center text-chrome-muted">{{ $round->starts_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td class="text-center text-chrome-muted">{{ $round->ends_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td class="text-center">
                                            <x-status-badge domain="match" :status="$round->status" set="bi" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <x-empty-state icon="bi-layers" title="{{ __('app.no_rounds_yet') }}" message="{{ __('app.no_rounds_yet_message') }}" />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-plus-lg me-1"></i> {{ __('app.add_round') }}</h6>
                </div>
                <div class="card-body p-4">
                    <form wire:submit="create">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.round_name') }}</label>
                            <input type="text" class="form-control" wire:model="name" required>
                            @error('name') <div class="text-danger fs-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.round_number') }}</label>
                            <input type="number" class="form-control" wire:model="number" min="1" required>
                            @error('number') <div class="text-danger fs-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.round_starts') }}</label>
                                <input type="text" class="form-control flatpickr-input" wire:model="starts_at" placeholder="{{ __('app.select_date_time') }}" data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                                @error('starts_at') <div class="text-danger fs-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.round_ends') }}</label>
                                <input type="text" class="form-control flatpickr-input" wire:model="ends_at" placeholder="{{ __('app.select_date_time') }}" data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                                @error('ends_at') <div class="text-danger fs-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning w-100" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="create"><i class="bi bi-check-lg"></i> {{ __('app.add_round') }}</span>
                            <span wire:loading wire:target="create"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
