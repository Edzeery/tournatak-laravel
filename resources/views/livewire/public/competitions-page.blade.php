<div>
    <section class="hero-sports hero-sports-sm text-white">
        <div class="container hero-content">
            <div class="text-center position-relative" class="pos-rel-z2">
                <div class="hero-badge mx-auto mb-3 d-inline-flex">
                    <i class="bi bi-trophy-fill"></i> {{ __('app.all_competitions') }}
                </div>
                <h1 class="fw-bold mb-3 fs-4xl">{{ __('app.competitions_hero_title') }}</h1>
                <p class="text-theme-muted hero-desc">
                    {{ __('app.competitions_hero_desc') }}
                </p>
            </div>
        </div>
        <div class="hero-gradient-bottom"></div>
    </section>

    <div class="container py-5 mt-neg-20">
        @if($competitions->count())
            <div class="row g-4">
                @foreach($competitions as $competition)
                    <div class="col-md-6 col-lg-4">
                        <div class="competition-card h-100">
                            <div class="card-header-custom"></div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $competition->name }}</h5>
                                    <x-status-badge domain="competition" status="{{ $competition->status }}" set="bi" />
                                </div>
                                @if($competition->type)
                                    <span class="badge-sport mb-2 d-inline-block">{{ $competition->type->name }}</span>
                                @endif
                                <p class="text-muted mb-3 fs-md lh-tight">
                                    {{ Str::limit($competition->description, 120) }}
                                </p>
                                <div class="card-meta">
                                    <span><i class="bi bi-calendar-event"></i> {{ formatDate($competition->start_date) }}</span>
                                    @if($competition->location)
                                        <span><i class="bi bi-geo-alt"></i> {{ Str::limit($competition->location, 25) }}</span>
                                    @endif
                                </div>
                                @if($competition->organizer)
                                    <div class="mt-2 fs-base text-chrome-muted">
                                        <i class="bi bi-person"></i> {{ __('app.organizer_label') }} {{ $competition->organizer->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $competitions->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-trophy d-block"></i>
                <h4>{{ __('app.no_competitions_yet') }}</h4>
                <p>{{ __('app.competitions_coming_soon_public') }}</p>
            </div>
        @endif
    </div>
</div>
