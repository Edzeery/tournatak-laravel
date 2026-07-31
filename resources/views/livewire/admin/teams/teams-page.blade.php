<div>
    <x-section-header
        icon="bi bi-shield-fill"
        :title="__('app.team_management')"
        :subtitle="__('app.teams_desc')"
        :breadcrumbs="[
            ['route' => route('admin.dashboard'), 'label' => __('app.dashboard')],
            ['label' => __('app.teams')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.teams.create') }}" class="btn btn-warning">
                <i class="bi bi-plus-lg"></i> {{ __('app.add_team') }}
            </a>
        </x-slot:action>
    </x-section-header>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_teams_placeholder') }}" wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
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
                            <th>{{ __('app.logo') }}</th>
                            <th>{{ __('app.team_name') }}</th>
                            <th>{{ __('app.captain') }}</th>
                            <th>{{ __('app.points') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $team)
                            <tr wire:key="{{ $team->id }}">
                                <td class="text-chrome-muted">{{ $team->id }}</td>
                                <td>
                                    @if($team->logo)
                                        <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" class="rounded-circle object-cover border-chrome w-38 h-38 logo-ring">
                                    @else
                                        <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold bg-opacity-10 w-38 h-38 fs-base">
                                            {{ mb_substr($team->name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $team->name }}</td>
                                <td>{{ $team->captain->name ?? '-' }}</td>
                                <td><span class="badge-sport"><i class="bi bi-star-fill"></i> {{ $team->points }}</span></td>
                                <td class="text-center d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-sm btn-outline-primary rounded-md"
                                        aria-label="{{ __('app.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.teams.staff', $team->id) }}" class="btn btn-sm btn-outline-info" title="{{ __('app.technical_staff') }}" aria-label="{{ __('app.technical_staff') }}"><i class="bi bi-people"></i></a>
                                    <a href="{{ route('admin.teams.formations', $team->id) }}" class="btn btn-sm btn-outline-success" title="{{ __('app.formations') }}" aria-label="{{ __('app.formations') }}"><i class="bi bi-grid-3x3-gap"></i></a>
                                    <a href="{{ route('admin.teams.tactics', $team->id) }}" class="btn btn-sm btn-outline-warning" title="{{ __('app.type') }}" aria-label="{{ __('app.type') }}"><i class="bi bi-diagram-3"></i></a>
                                    <a href="{{ route('admin.teams.medical', $team->id) }}" class="btn btn-sm btn-outline-danger" title="{{ __('app.medical') }}" aria-label="{{ __('app.medical') }}"><i class="bi bi-heart-pulse"></i></a>
                                    <a href="{{ route('admin.teams.stats', $team->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.results') }}" aria-label="{{ __('app.results') }}"><i class="bi bi-bar-chart"></i></a>
                                    <button class="btn btn-sm btn-outline-danger rounded-md"
                                            wire:click="delete({{ $team->id }})"
                                            wire:confirm="{{ __('app.confirm_delete_team') }}"
                                            aria-label="{{ __('app.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="6">
                                    <x-empty-state icon="bi-people" title="{{ __('app.teams') }}" message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $teams->links() }}</div>
</div>
