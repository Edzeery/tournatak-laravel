@extends('layouts.app')

@section('title', 'الفرق')

@section('content')
<div>
    {{-- Page Header --}}
    <section class="hero-sports text-white" style="min-height:300px;">
        <div class="container hero-content">
            <div class="text-center" style="position:relative;z-index:2;">
                <div class="hero-badge mx-auto mb-3" style="display:inline-flex;">
                    <i class="bi bi-shield-fill"></i> جميع الفرق
                </div>
                <h1 class="fw-bold mb-3" style="font-size:2.5rem;">الفرق المسجلة</h1>
                <p style="color:rgba(255,255,255,0.6);max-width:500px;margin:0 auto;">
                    تعرف على الفرق المشاركة في البطولات والمسابقات
                </p>
            </div>
        </div>
        <div style="position:absolute;bottom:0;left:0;right:0;height:80px;background:linear-gradient(to top, #f5f6fa, transparent);"></div>
    </section>

    <div class="container py-5" style="margin-top:-20px;">
        @php
            $teams = \App\Models\Team::with('captain')->latest()->paginate(12);
        @endphp

        @if($teams->count())
            <div class="row g-4">
                @foreach($teams as $team)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="team-card h-100">
                            @if($team->logo)
                                <img src="{{ asset('uploads/teams/' . $team->logo) }}" alt="{{ $team->name }}" class="rounded-circle mb-3" width="80" height="80" style="object-fit: cover; border: 3px solid var(--primary);">
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
                                <i class="bi bi-star-fill"></i> {{ $team->points ?? 0 }} نقطة
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
                <h4>لا توجد فرق حالياً</h4>
                <p>سيتم عرض الفرق هنا قريباً</p>
            </div>
        @endif
    </div>
</div>
@endsection
