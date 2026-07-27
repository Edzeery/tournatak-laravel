@extends('layouts.app')

@section('title', __('app.players'))

@section('content')
<div>
    <section class="hero-sports hero-sports-sm text-white">
        <div class="container hero-content">
            <div class="text-center position-relative" style="z-index:2;">
                <div class="hero-badge mx-auto mb-3 d-inline-flex">
                    <i class="bi bi-people-fill"></i> {{ __('app.all_players') }}
                </div>
                <h1 class="fw-bold mb-3 fs-4xl">{{ __('app.players_hero_title') }}</h1>
                <p class="text-theme-muted hero-desc">
                    {{ __('app.players_hero_desc') }}
                </p>
            </div>
        </div>
        <div class="hero-gradient-bottom"></div>
    </section>

    <div class="container py-5 mt-neg-20">
        @php
            $players = \App\Models\Player::with(['user', 'team'])
                ->withCount('goals')
                ->latest()
                ->paginate(12);
        @endphp

        @if($players->count())
            <div class="row g-4">
                @foreach($players as $player)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="team-card h-100">
                            @if($player->image)
                                <img src="{{ asset('uploads/players/' . $player->image) }}" alt="{{ $player->user->name ?? '' }}" class="rounded-circle mb-3 logo-ring" width="80" height="80">
                            @else
                                <div class="team-avatar mx-auto player-avatar-blue">
                                    {{ mb_substr($player->user->name ?? 'P', 0, 1) }}
                                </div>
                            @endif
                            <h6 class="mb-1 fw-bold">{{ $player->user->name ?? __('app.player_fallback') }}</h6>
                            @if($player->team)
                                <small class="text-muted d-block mb-2">
                                    <i class="bi bi-shield"></i> {{ $player->team->name }}
                                </small>
                            @endif
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                @if($player->number)
                                    <span class="badge-sport">#{{ $player->number }}</span>
                                @endif
                                <span class="badge-sport">
                                    <i class="bi bi-circle-fill fs-2xs"></i> {{ $player->goals_count }} {{ __('app.goals') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $players->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-person d-block"></i>
                <h4>{{ __('app.no_players_yet') }}</h4>
                <p>{{ __('app.players_coming_soon') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
