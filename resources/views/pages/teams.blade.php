@extends('layouts.app')

@section('title', __('app.teams'))

@section('content')
<div>
    <section class="hero-sports hero-sports-sm text-white">
        <div class="container hero-content">
            <div class="text-center position-relative" class="pos-rel-z2">
                <div class="hero-badge mx-auto mb-3 d-inline-flex">
                    <i class="bi bi-shield-fill"></i> {{ __('app.all_teams') }}
                </div>
                <h1 class="fw-bold mb-3 fs-4xl">{{ __('app.teams_hero_title') }}</h1>
                <p class="text-theme-muted hero-desc">
                    {{ __('app.teams_hero_desc') }}
                </p>
            </div>
        </div>
        <div class="hero-gradient-bottom"></div>
    </section>

    <div class="container py-5 mt-neg-20">
        @php
            $teams = \App\Models\Team::with('captain')->latest()->paginate(12);
        @endphp

        @if($teams->count())
            <div class="row g-4">
                @foreach($teams as $team)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="team-card h-100">
                            @if($team->logo)
                                <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" class="rounded-circle mb-3 logo-ring" width="80" height="80">
                            @else
                                <div class="team-avatar bg-gold text-dark mx-auto">
                                    {{ mb_substr($team->name, 0, 1) }}
                                </div>
                            @endif
                            <h6 class="mb-1 fw-bold">{{ $team->name }}</h6>
                            @if($team->captain)
                                <small class="text-muted d-block mb-2">
                                    <i class="bi bi-person"></i> {{ $team->captain->name }}
                                </small>
                            @endif
                            <span class="badge-sport">
                                <i class="bi bi-star-fill"></i> {{ $team->points ?? 0 }} {{ __('app.points') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $teams->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-shield d-block"></i>
                <h4>{{ __('app.no_teams_yet') }}</h4>
                <p>{{ __('app.teams_coming_soon') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
