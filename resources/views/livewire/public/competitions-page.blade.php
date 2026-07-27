<div>
    <section class="hero-sports text-white" style="min-height:300px;">
        <div class="container hero-content">
            <div class="text-center" style="position:relative;z-index:2;">
                <div class="hero-badge mx-auto mb-3" style="display:inline-flex;">
                    <i class="bi bi-trophy-fill"></i> جميع البطولات
                </div>
                <h1 class="fw-bold mb-3" style="font-size:2.5rem;">البطولات والمسابقات</h1>
                <p style="color:rgba(255,255,255,0.6);max-width:500px;margin:0 auto;">
                    تصفح جميع البطولات والمسابقات الرياضية المسجلة في المنصة
                </p>
            </div>
        </div>
        <div style="position:absolute;bottom:0;left:0;right:0;height:80px;background:linear-gradient(to top, #f5f6fa, transparent);"></div>
    </section>

    <div class="container py-5" style="margin-top:-20px;">
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
                                <p class="text-muted mb-3" style="font-size:0.9rem;line-height:1.6;">
                                    {{ Str::limit($competition->description, 120) }}
                                </p>
                                <div class="card-meta">
                                    <span><i class="bi bi-calendar-event"></i> {{ $competition->start_date?->format('Y/m/d') }}</span>
                                    @if($competition->location)
                                        <span><i class="bi bi-geo-alt"></i> {{ Str::limit($competition->location, 25) }}</span>
                                    @endif
                                </div>
                                @if($competition->organizer)
                                    <div class="mt-2" style="font-size:0.85rem;color:#94a3b8;">
                                        <i class="bi bi-person"></i> المنظم: {{ $competition->organizer->name }}
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
                <h4>لا توجد بطولات حالياً</h4>
                <p>سيتم عرض البطولات هنا بعد الموافقة عليها</p>
            </div>
        @endif
    </div>
</div>
