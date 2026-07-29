<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb fs-base">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.index') }}" class="breadcrumb-link">{{ __('app.matches') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.edit', $match) }}" class="breadcrumb-link">{{ $match->team1->name ?? '?' }} vs {{ $match->team2->name ?? '?' }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.match_events') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary"><i class="bi bi-lightning-fill text-gold"></i> {{ __('app.match_events') }}</h4>
            <p class="text-muted mb-0 fs-md">
                {{ $match->team1->name ?? '?' }}
                <span class="text-gold fw-bold mx-1">vs</span>
                {{ $match->team2->name ?? '?' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-warning" wire:click="$set('showModal', true)">
                <i class="bi bi-plus-lg"></i> {{ __('app.add_event') }}
            </button>
            <a href="{{ route('admin.matches.edit', $match) }}" class="btn btn-outline-secondary rounded-md">
                <i class="bi bi-arrow-right"></i> {{ __('app.back') }}
            </a>
        </div>
    </div>

    <div class="card border-0" wire:loading.opacity>
        <div class="card-body">
            @forelse($events as $event)
                @php
                    $typeColors = [
                        'goal' => ['bg' => 'success', 'icon' => 'bi-circle-fill'],
                        'own_goal' => ['bg' => 'success', 'icon' => 'bi-circle-fill'],
                        'penalty_scored' => ['bg' => 'success', 'icon' => 'bi-circle-fill'],
                        'penalty_missed' => ['bg' => 'danger', 'icon' => 'bi-circle-fill'],
                        'yellow_card' => ['bg' => 'warning', 'icon' => 'bi-square-fill'],
                        'second_yellow' => ['bg' => 'warning', 'icon' => 'bi-square-fill'],
                        'red_card' => ['bg' => 'danger', 'icon' => 'bi-square-fill'],
                        'substitution_in' => ['bg' => 'primary', 'icon' => 'bi-arrow-left-right'],
                        'substitution_out' => ['bg' => 'primary', 'icon' => 'bi-arrow-left-right'],
                        'injury' => ['bg' => 'danger', 'icon' => 'bi-heart-pulse'],
                        'save' => ['bg' => 'info', 'icon' => 'bi-handbag'],
                        'assist' => ['bg' => 'info', 'icon' => 'bi-arrow-return-left'],
                    ];
                    $color = $typeColors[$event->event_type] ?? ['bg' => 'secondary', 'icon' => 'bi-circle'];
                    $eventTeam = $event->team_id == $match->team1_id ? $match->team1 : $match->team2;
                @endphp
                <div class="d-flex align-items-start gap-3 mb-3 p-3 rounded-3 " wire:key="event-{{ $event->id }}">
                    <div class="text-center flex-shrink-0 min-w-60">
                        <span class="badge bg-chrome rounded-pill badge-lg min-w-50">
                            {{ $event->minute }}'
                            @if($event->added_time)
                                +{{ $event->added_time }}
                            @endif
                        </span>
                    </div>

                    <div class="flex-shrink-0 mt-1">
                        <span class="badge bg-{{ $color['bg'] }} rounded-circle d-flex align-items-center justify-content-center w-36 h-36">
                            <i class="bi {{ $color['icon'] }}"></i>
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-{{ $color['bg'] }} text-white fs-sm">
                                {{ $eventTypes[$event->event_type] ?? $event->event_type }}
                            </span>
                            <strong>{{ $event->player->name ?? '-' }}</strong>
                            <span class="text-muted">·</span>
                            <small class="text-muted">{{ $eventTeam->name ?? '-' }}</small>
                        </div>
                        @if(in_array($event->event_type, ['substitution_in', 'substitution_out']) && $event->relatedPlayer)
                            <div class="mt-1 fs-base">
                                <i class="bi bi-arrow-left-right text-primary"></i>
                                <span class="text-muted">{{ __('app.related_player') }}</span>
                                <strong>{{ $event->relatedPlayer->name }}</strong>
                            </div>
                        @endif
                        @if($event->description)
                            <div class="mt-1">
                                <small class="text-muted fs-base"><i class="bi bi-chat-left-text"></i> {{ $event->description }}</small>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex gap-1 flex-shrink-0">
                        <button class="btn btn-sm btn-outline-primary rounded-md" wire:click="editEvent({{ $event->id }})"
                            aria-label="{{ __('app.edit') }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-md"
                                wire:click="deleteEvent({{ $event->id }})"
                                wire:confirm="{{ __('app.confirm_delete_event') }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state py-5 text-center">
                    <i class="bi bi-calendar-event d-block text-muted fs-4xl"></i>
                    <h5 class="text-muted mt-2">{{ __('app.no_events') }}</h5>
                    <p class="text-muted fs-base">{{ __('app.no_events_desc') }}</p>
                    <button class="btn btn-warning mt-2" wire:click="$set('showModal', true)">
                        <i class="bi bi-plus-lg"></i> {{ __('app.add_first_event') }}
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    @if($showModal)
        <div class="modal d-block modal-backdrop-dark" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="eventModalTitle" wire:click.self="closeModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" @click.away="closeModal">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="eventModalTitle">
                            <i class="bi bi-lightning-fill text-gold"></i>
                            {{ $editingEventId ? __('app.edit_event') : __('app.add_event') }}
                        </h5>
                        <button type="button" class="btn-close" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('app.team') }}</label>
                                <select class="form-select" wire:model.live="eventForm.team_id">
                                    <option value="">{{ __('app.choose_team_placeholder') }}</option>
                                    <option value="{{ $match->team1_id }}">{{ $match->team1->name ?? __('app.team1_name') }}</option>
                                    <option value="{{ $match->team2_id }}">{{ $match->team2->name ?? __('app.team2_name') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('app.event_type') }}</label>
                                <select class="form-select" wire:model="eventForm.event_type">
                                    <option value="">{{ __('app.choose_type_placeholder') }}</option>
                                    @foreach($eventTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('app.player') }}</label>
                                <select class="form-select" wire:model="eventForm.player_id">
                                    <option value="">{{ __('app.choose_player') }}</option>
                                    @foreach($activeTeamPlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->number ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('app.related_player_label') }}</label>
                                <select class="form-select" wire:model="eventForm.related_player_id" {{ in_array($eventForm['event_type'], ['substitution_in', 'substitution_out']) ? '' : 'disabled' }}>
                                    <option value="">{{ __('app.none') }}</option>
                                    @foreach($activeTeamPlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->number ?? '-' }})</option>
                                    @endforeach
                                </select>
                                @if(!in_array($eventForm['event_type'], ['substitution_in', 'substitution_out']))
                                    <small class="text-muted fs-sm">{{ __('app.substitution_hint') }}</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.minute') }}</label>
                                <input type="number" class="form-control" wire:model="eventForm.minute" min="0" max="120" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('app.added_time') }}</label>
                                <input type="number" class="form-control" wire:model="eventForm.added_time" min="0" max="15" placeholder="0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">{{ __('app.description') }}</label>
                                <textarea class="form-control" wire:model="eventForm.description" rows="2" placeholder="{{ __('app.event_description_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('app.cancel') }}</button>
                        <button type="button" class="btn btn-warning px-4" wire:click="saveEvent" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveEvent"><i class="bi bi-check-lg"></i> {{ __('app.save') }}</span>
                            <span wire:loading wire:target="saveEvent"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
