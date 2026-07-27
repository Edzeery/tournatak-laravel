@extends('layouts.app')

@section('title', 'اللاعبون')

@section('content')
<div>
    {{-- Page Header --}}
    <section class="hero-sports text-white" style="min-height:300px;">
        <div class="container hero-content">
            <div class="text-center" style="position:relative;z-index:2;">
                <div class="hero-badge mx-auto mb-3" style="display:inline-flex;">
                    <i class="bi bi-people-fill"></i> جميع اللاعبين
                </div>
                <h1 class="fw-bold mb-3" style="font-size:2.5rem;">اللاعبون</h1>
                <p style="color:rgba(255,255,255,0.6);max-width:500px;margin:0 auto;">
                    تعرف على اللاعبين المسجلين في المنصة وإحصائياتهم
                </p>
            </div>
        </div>
        <div style="position:absolute;bottom:0;left:0;right:0;height:80px;background:linear-gradient(to top, #f5f6fa, transparent);"></div>
    </section>

    <div class="container py-5" style="margin-top:-20px;">
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
                                <img src="{{ asset('uploads/players/' . $player->image) }}" alt="{{ $player->user->name ?? '' }}" class="rounded-circle mb-3" width="80" height="80" style="object-fit: cover; border: 3px solid var(--primary);">
                            @else
                                <div class="team-avatar mx-auto" style="background:rgba(59,130,246,0.15);color:#3b82f6;">
                                    {{ mb_substr($player->user->name ?? 'ل', 0, 1) }}
                                </div>
                            @endif
                            <h6 class="mb-1 fw-bold">{{ $player->user->name ?? 'لاعب' }}</h6>
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
                                    <i class="bi bi-circle-fill" style="font-size:0.5rem;"></i> {{ $player->goals_count }} هدف
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
                <h4>لا يوجد لاعبون حالياً</h4>
                <p>سيتم عرض اللاعبين هنا قريباً</p>
            </div>
        @endif
    </div>
</div>
@endsection
