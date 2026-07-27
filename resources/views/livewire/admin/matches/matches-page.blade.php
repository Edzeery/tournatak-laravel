<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-calendar-event-fill text-gold"></i> {{ __('app.match_management') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ __('app.matches_desc') }}</p>
        </div>
        <a href="{{ route('admin.matches.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_match') }}
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.matches') }}</li>
        </ol>
    </nav>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_matches_placeholder') }}" wire:model.live.debounce.300ms="search">
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
                            <th>{{ __('app.competitions') }}</th>
                            <th>{{ __('app.match') }}</th>
                            <th>{{ __('app.score') }}</th>
                            <th>{{ __('app.date') }}</th>
                            <th>{{ __('app.status') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matches as $match)
                            <tr wire:key="{{ $match->id }}">
                                <td class="text-chrome-muted">{{ $match->id }}</td>
                                <td>{{ $match->competition->name ?? '-' }}</td>
                                <td class="fw-bold">
                                    {{ $match->team1->name ?? '?' }}
                                    <span class="mx-1 fw-bold" style="color:#b8860b">vs</span>
                                    {{ $match->team2->name ?? '?' }}
                                </td>
                                <td>
                                    <span class="badge-sport fs-md">
                                        {{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}
                                    </span>
                                </td>
                                <td class="fs-base text-chrome-muted">
                                    {{ formatDateTime($match->match_date) ?? '-' }}
                                </td>
                                <td>
                                    <x-status-badge domain="match" status="{{ $match->status }}" set="bi" />
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.matches.edit', $match) }}" class="btn btn-sm btn-outline-primary rounded-md"
                                        aria-label="{{ __('app.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.matches.lineup', $match->id) }}" class="btn btn-sm btn-outline-success" title="{{ __('app.lineup') }}" aria-label="{{ __('app.lineup') }}"><i class="bi bi-people-fill"></i></a>
                                    <a href="{{ route('admin.matches.events', $match->id) }}" class="btn btn-sm btn-outline-warning" title="{{ __('app.events') }}" aria-label="{{ __('app.events') }}"><i class="bi bi-clock-history"></i></a>
                                    <a href="{{ route('admin.matches.stats', $match->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.results') }}" aria-label="{{ __('app.results') }}"><i class="bi bi-bar-chart-line"></i></a>
                                    <button class="btn btn-sm btn-outline-danger rounded-md"
                                            wire:click="delete({{ $match->id }})"
                                            wire:confirm="{{ __('app.confirm_delete_match') }}"
                                            aria-label="{{ __('app.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="7">
                                    <x-empty-state icon="bi-calendar2-event" title="{{ __('app.matches') }}" message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $matches->links() }}</div>
</div>
