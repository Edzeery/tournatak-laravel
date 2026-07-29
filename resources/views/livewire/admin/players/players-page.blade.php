<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-person-badge-fill text-gold"></i> {{ __('app.player_management') }}</h4>
            <p class="text-muted mb-0 fs-md">{{ __('app.players_desc') }}</p>
        </div>
        <a href="{{ route('admin.players.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_player') }}
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.players') }}</li>
        </ol>
    </nav>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <form wire:submit="resetPage" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold fs-base">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search_players_placeholder') }}" wire:model.live.debounce.300ms="search" aria-label="{{ __('app.search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold fs-base">{{ __('app.per_page_display') }}</label>
                    <select class="form-select" wire:model.live="perPage" aria-label="{{ __('app.per_page') }}">
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
                            <th>{{ __('app.image') }}</th>
                            <th>{{ __('app.name') }}</th>
                            <th>{{ __('app.teams') }}</th>
                            <th>{{ __('app.number') }}</th>
                            <th>{{ __('app.center') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($players as $player)
                            <tr wire:key="{{ $player->id }}">
                                <td class="text-chrome-muted">{{ $player->id }}</td>
                                <td>
                                    @if($player->image)
                                        <img src="{{ $player->image_url }}" alt="" class="rounded-circle object-cover border-chrome w-38 h-38 logo-ring">
                                    @else
                                        <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold bg-opacity-10 w-38 h-38 fs-base">
                                            {{ mb_substr($player->user->name ?? '?', 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $player->user->name ?? '-' }}</td>
                                <td>{{ $player->team->name ?? '-' }}</td>
                                <td><span class="badge-sport">{{ $player->number ?? '-' }}</span></td>
                                <td>{{ $player->position->name ?? $player->position_text ?? '-' }}</td>
                                <td class="text-center d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.players.edit', $player) }}" class="btn btn-sm btn-outline-primary rounded-md"
                                        aria-label="{{ __('app.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger rounded-md"
                                            wire:click="delete({{ $player->id }})"
                                            wire:confirm="{{ __('app.confirm_delete_player') }}"
                                            aria-label="{{ __('app.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:loading.remove>
                                <td colspan="7">
                                    <x-empty-state icon="bi-person-raised-hand" title="{{ __('app.players') }}" message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $players->links() }}</div>
</div>
