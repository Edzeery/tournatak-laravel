<div>
    <section class="hero-sports text-white" style="min-height:280px;">
        <div class="container hero-content">
            <div class="d-flex align-items-center gap-4 position-relative" style="z-index:2;">
                @if($player->image)
                    <img src="{{ asset('uploads/players/' . $player->image) }}" alt="{{ $player->name ?? '' }}" class="rounded-circle object-cover player-hero-img" width="110" height="110">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-gold text-dark fw-bold w-110 h-110 fs-4xl">
                        {{ mb_substr($player->name ?? 'P', 0, 1) }}
                    </div>
                @endif
                <div>
                    @if($player->number)
                        <span class="badge bg-gold text-dark mb-1 fs-md">#{{ $player->number }}</span>
                    @endif
                    <h1 class="fw-bold mb-1 fs-3xl">{{ $player->name ?? __('app.player_fallback') }}</h1>
                    <p class="player-hero-subtitle">
                        @if($player->team)
                            <i class="bi bi-shield-fill"></i>
                            <a href="{{ route('teams.show', $player->team->id) }}" class="text-white text-decoration-underline">{{ $player->team->name }}</a>
                        @endif
                        @if($player->position)
                            <span class="mx-2">|</span>
                            <i class="bi bi-geo-alt-fill"></i> {{ $player->position->name }}
                        @endif
                    </p>
                    @if($player->is_captain)
                        <span class="badge bg-warning text-dark mt-1 fs-sm">{{ __('app.captain_badge') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="hero-gradient-bottom"></div>
    </section>

    <div class="container py-5 mt-neg-20">
        <div class="row g-4">
            {{-- Main Info --}}
            <div class="col-lg-8">
                @if($player->bio)
                    <div class="card border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2 text-theme-primary">
                                <i class="bi bi-person-lines-fill text-gold"></i> {{ __('app.bio') }}
                            </h6>
                            <p class="fs-md player-bio-text">{{ $player->bio }}</p>
                        </div>
                    </div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="card border-0 text-center rounded-lg-custom">
                            <div class="card-body py-3">
                                <div class="fw-bold fs-2xl text-gold">{{ $totalGoals }}</div>
                                <small class="text-muted player-stat-sm">{{ __('app.goals_abbr') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 text-center rounded-lg-custom">
                            <div class="card-body py-3">
                                <div class="fw-bold fs-2xl text-gold">{{ $player->lineups()->count() }}</div>
                                <small class="text-muted player-stat-sm">{{ __('app.match') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 text-center rounded-lg-custom">
                            <div class="card-body py-3">
                                <div class="fw-bold fs-2xl text-gold">{{ $player->lineups()->where('is_starter', true)->count() }}</div>
                                <small class="text-muted player-stat-sm">{{ __('app.lineup') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if($goals->count())
                    <div class="card border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-theme-primary">
                                <i class="bi bi-circle-fill text-gold fs-xs"></i> {{ __('app.goals') }}
                            </h6>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="fs-xs">{{ __('app.match') }}</th>
                                            <th class="fs-xs">{{ __('app.date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($goals as $goal)
                                            <tr>
                                                <td class="fs-base">
                                                    {{ $goal->match->team1->name ?? '?' }} vs {{ $goal->match->team2->name ?? '?' }}
                                                </td>
                                                <td class="text-theme-muted fs-xs">
                                                    {{ formatDate($goal->match->match_date) ?? '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if($seasonStats->count())
                    <div class="card border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-theme-primary">
                                <i class="bi bi-bar-chart-line text-gold"></i> {{ __('app.season_stats') }}
                            </h6>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="fs-xs">{{ __('app.competitions') }}</th>
                                            <th class="text-center fs-xs">{{ __('app.matches_abbr') }}</th>
                                            <th class="text-center fs-xs">{{ __('app.goals_abbr') }}</th>
                                            <th class="text-center fs-xs">{{ __('app.assists') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($seasonStats as $stat)
                                            <tr>
                                                <td class="fs-base">{{ $stat->competition->name ?? '—' }}</td>
                                                <td class="text-center fs-base">{{ $stat->matches_played ?? 0 }}</td>
                                                <td class="text-center fw-bold fs-base text-gold">{{ $stat->goals ?? 0 }}</td>
                                                <td class="text-center fs-base">{{ $stat->assists ?? 0 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="card border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-theme-primary">
                            <i class="bi bi-info-circle text-gold"></i> {{ __('app.player_info') }}
                        </h6>
                        <div class="d-flex flex-column gap-2">
                            @if($player->position)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted fs-base">{{ __('app.center') }}</span>
                                    <span class="fw-bold fs-base">{{ $player->position->name }}</span>
                                </div>
                            @endif
                            @if($player->date_of_birth)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted fs-base">{{ __('app.date_of_birth') }}</span>
                                    <span class="fw-bold fs-base">{{ formatDate($player->date_of_birth) }}</span>
                                </div>
                            @endif
                            @if($player->nationality)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted fs-base">{{ __('app.nationality') }}</span>
                                    <span class="fw-bold fs-base">{{ $player->nationality }}</span>
                                </div>
                            @endif
                            @if($player->height)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted fs-base">{{ __('app.height') }}</span>
                                    <span class="fw-bold fs-base">{{ $player->height }} {{ __('app.cm') }}</span>
                                </div>
                            @endif
                            @if($player->weight)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted fs-base">{{ __('app.weight') }}</span>
                                    <span class="fw-bold fs-base">{{ $player->weight }} {{ __('app.kg') }}</span>
                                </div>
                            @endif
                            @if($player->foot)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted fs-base">{{ __('app.foot') }}</span>
                                    <span class="fw-bold fs-base">{{ $player->foot === 'right' ? __('app.right') : ($player->foot === 'left' ? __('app.left') : __('app.both')) }}</span>
                                </div>
                            @endif
                            @if($player->sport_type)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted fs-base">{{ __('app.sport_type_label') }}</span>
                                    <span class="fw-bold fs-base">{{ $player->sport_type === 'football' ? __('app.football') : __('app.futsal') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
