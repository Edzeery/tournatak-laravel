<div>
    <section class="hero-sports text-white" style="min-height:250px;">
        <div class="container hero-content">
            <div class="d-flex align-items-center gap-4 position-relative" class="pos-rel-z2">
                @if($team->logo)
                    <img src="{{ asset('uploads/teams/' . $team->logo) }}" alt="{{ $team->name }}" class="rounded-circle team-hero-img" width="100" height="100">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-gold text-dark fw-bold team-hero-avatar">
                        {{ mb_substr($team->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="fw-bold mb-1 team-hero-name">{{ $team->name }}</h1>
                    @if($team->captain)
                        <p class="team-hero-subtitle">
                            <i class="bi bi-person-fill"></i> {{ __('app.captain_label') }} {{ $team->captain->name }}
                        </p>
                    @endif
                    <span class="badge bg-gold text-dark mt-2 badge-points">
                        <i class="bi bi-star-fill"></i> {{ $team->points ?? 0 }} {{ __('app.points') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="hero-gradient-bottom-sm"></div>
    </section>

    <div class="container py-5 mt-neg-20">
        <div class="row g-4">
            {{-- Players --}}
            <div class="col-lg-8">
                <div class="card border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 section-title-dark">
                            <i class="bi bi-people-fill text-gold"></i> {{ __('app.players') }}
                            <span class="badge bg-secondary rounded-pill ms-2 badge-count">{{ $players->count() }}</span>
                        </h5>
                        @if($players->count())
                            <div class="row g-3">
                                @foreach($players as $player)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ route('players.show', $player->id) }}" class="text-decoration-none">
                                            <div class="d-flex align-items-center gap-3 p-2 rounded-3 team-player-card">
                                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 team-player-avatar">
                                                    {{ $player->number ?? '?' }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold item-name text-dark-theme">{{ $player->name ?? __('app.player_fallback') }}</div>
                                                    <small class="text-muted item-sub">
                                                        {{ $player->position->name ?? '—' }}
                                                        @if($player->is_captain)
                                                            <span class="text-warning"> (C)</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-people d-block mb-2 team-empty-icon"></i>
                                <p class="section-desc">{{ __('app.no_players_registered') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar: Staff + Formations --}}
            <div class="col-lg-4">
                {{-- Staff --}}
                <div class="card border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 section-title-dark">
                            <i class="bi bi-person-workspace text-gold"></i> {{ __('app.technical_staff') }}
                        </h6>
                        @if($staff->count())
                            @foreach($staff as $member)
                                <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-light team-staff-avatar">
                                        <i class="bi bi-award text-gold team-staff-icon"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold item-name">{{ $member->user->name ?? '—' }}</div>
                                        <small class="text-muted item-sub">{{ \App\Models\TeamStaff::STAFF_ROLES[$member->staff_role] ?? $member->staff_role }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted">
                                <small class="item-name">{{ __('app.no_staff_assigned') }}</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Formations --}}
                <div class="card border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 section-title-dark">
                            <i class="bi bi-diagram-3 text-gold"></i> {{ __('app.formations') }}
                        </h6>
                        @if($formations->count())
                            @foreach($formations->take(4) as $formation)
                                <div class="d-flex align-items-center justify-content-between {{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
                                    <div>
                                        <div class="fw-bold item-name">{{ $formation->name }}</div>
                                        <small class="text-muted item-sub">{{ $formation->formation_code }}</small>
                                    </div>
                                    @if($formation->is_default)
                                        <span class="badge bg-gold text-dark badge-formation-default">{{ __('app.default') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted">
                                <small class="item-name">{{ __('app.no_formations_created') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
