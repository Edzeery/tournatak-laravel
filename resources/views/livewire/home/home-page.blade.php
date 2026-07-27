<div>
    {{-- Hero Section --}}
    <section class="hero-sports text-white">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-badge animate-in">
                        <i class="bi bi-lightning-charge-fill"></i> المنصة الرياضية الأولى
                    </div>
                    <h1 class="hero-title mb-4 animate-in animate-delay-1">
                        إدارة البطولات<br>
                        <span class="text-gold">بشكل احترافي</span>
                    </h1>
                    <p class="hero-subtitle mb-5 animate-in animate-delay-2">
                        منصة متكاملة لإدارة البطولات والمسابقات الرياضية، تتبع الفرق واللاعبين، وتسجيل المباريات والنتائج في الوقت الحقيقي.
                    </p>
                    <div class="d-flex gap-3 flex-wrap animate-in animate-delay-3">
                        @auth
                            <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-primary-sport btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i> لوحة التحكم
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary-sport btn-lg">
                                <i class="bi bi-rocket-takeoff me-2"></i> سجل مجاناً
                            </a>
                        @endauth
                        <a href="{{ route('competitions.index') }}" class="btn btn-outline-sport btn-lg">
                            <i class="bi bi-trophy me-2"></i> تصفح البطولات
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="position-relative">
                        <div class="hero-shape" style="width:350px;height:350px;top:-20px;right:20px;"></div>
                        <div class="hero-shape" style="width:200px;height:200px;bottom:20px;left:0;"></div>
                        <div class="position-relative" style="z-index:2;">
                            <div class="d-inline-flex flex-column align-items-center gap-3 p-4 rounded-4" style="background:rgba(255,255,255,0.04);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.08);">
                                <div class="d-flex gap-4">
                                    <div class="text-center">
                                        <div class="text-gold fw-bold" style="font-size:2.5rem;">{{ $stats['competitions'] ?? 0 }}</div>
                                        <small style="color:rgba(255,255,255,0.5);">بطولة</small>
                                    </div>
                                    <div style="width:1px;background:rgba(255,255,255,0.1);"></div>
                                    <div class="text-center">
                                        <div class="text-gold fw-bold" style="font-size:2.5rem;">{{ $stats['teams'] ?? 0 }}</div>
                                        <small style="color:rgba(255,255,255,0.5);">فريق</small>
                                    </div>
                                </div>
                                <div style="width:100%;height:1px;background:rgba(255,255,255,0.08);"></div>
                                <div class="text-center">
                                    <div class="text-gold fw-bold" style="font-size:2.5rem;">{{ $stats['players'] ?? 0 }}</div>
                                    <small style="color:rgba(255,255,255,0.5);">لاعب مسجل</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="position:absolute;bottom:0;left:0;right:0;height:120px;background:linear-gradient(to top, #f5f6fa, transparent);"></div>
    </section>

    {{-- Stats Section --}}
    <section class="stats-section py-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4 animate-in animate-delay-1">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning bg-opacity-10 text-gold">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div class="stat-number">{{ $stats['competitions'] ?? 0 }}</div>
                        <div class="stat-label">بطولة نشطة</div>
                    </div>
                </div>
                <div class="col-md-4 animate-in animate-delay-2">
                    <div class="stat-card">
                        <div class="stat-icon bg-success bg-opacity-10" style="color:#16a34a;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="stat-number">{{ $stats['teams'] ?? 0 }}</div>
                        <div class="stat-label">فريق مسجل</div>
                    </div>
                </div>
                <div class="col-md-4 animate-in animate-delay-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-number">{{ $stats['players'] ?? 0 }}</div>
                        <div class="stat-label">لاعب</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Active Competitions --}}
    <section class="py-5 mt-4">
        <div class="container">
            <div class="section-header">
                <div class="section-badge"><i class="bi bi-fire"></i> الأحدث</div>
                <h2>البطولات النشطة</h2>
                <p>تابع أحدث البطولات والمسابقات الرياضية المسجلة في المنصة</p>
            </div>

            @if($activeCompetitions->count())
                <div class="row g-4">
                    @foreach($activeCompetitions as $competition)
                        <div class="col-md-6 col-lg-4">
                            <div class="competition-card h-100">
                                <div class="card-header-custom"></div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">{{ $competition->name }}</h5>
                                        <x-status-badge domain="competition" status="{{ $competition->status }}" set="bi" />
                                    </div>
                                    @if($competition->description)
                                        <p class="text-muted mb-3" style="font-size:0.9rem;line-height:1.6;">
                                            {{ Str::limit($competition->description, 120) }}
                                        </p>
                                    @endif
                                    <div class="card-meta">
                                        <span><i class="bi bi-calendar-event"></i> {{ $competition->start_date?->format('Y/m/d') }}</span>
                                        @if($competition->organizer)
                                            <span><i class="bi bi-person"></i> {{ $competition->organizer->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-trophy d-block"></i>
                    <h4>لا توجد بطولات نشطة حالياً</h4>
                    <p>سيتم إضافة البطولات قريباً</p>
                </div>
            @endif

            <div class="text-center mt-5">
                <a href="{{ route('competitions.index') }}" class="btn btn-outline-sport">
                    {{ __('app.competitions') }} <i class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }} me-1"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Latest Teams --}}
    @if($teams->count())
    <section class="py-5" style="background:#fff;">
        <div class="container">
            <div class="section-header">
                <div class="section-badge"><i class="bi bi-shield-fill"></i> الفرق</div>
                <h2>أحدث الفرق المسجلة</h2>
                <p>تعرف على الفرق المشاركة في البطولات</p>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($teams as $team)
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="team-card">
                            @if($team->logo)
                                <img src="{{ asset('uploads/teams/' . $team->logo) }}" alt="{{ $team->name }}" class="rounded-circle mb-3" width="72" height="72" style="object-fit: cover; border: 3px solid var(--primary);">
                            @else
                                <div class="team-avatar bg-gold text-dark mx-auto">
                                    {{ mb_substr($team->name, 0, 1) }}
                                </div>
                            @endif
                            <h6 class="mb-1 fw-bold">{{ $team->name }}</h6>
                            @if($team->points)
                                <small class="text-muted"><i class="bi bi-star-fill text-gold"></i> {{ $team->points }} نقطة</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('teams.index') }}" class="btn btn-outline-sport">
                    {{ __('app.teams') }} <i class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }} me-1"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- CTA Section --}}
    <section class="py-5" style="background: var(--gradient-hero); position: relative; overflow: hidden;">
        <div class="container text-center position-relative" style="z-index:2;">
            <h2 class="text-white fw-bold mb-3" style="font-size:2rem;">هل أنت مستعد للبدء؟</h2>
            <p class="mb-4" style="color:rgba(255,255,255,0.6); max-width:500px; margin:0 auto;">
                انضم إلى منصة تورناتك وأدر بطولتك الخاصة أو سجّل فريقك وشارك في المسابقات الرياضية
            </p>
            @auth
                <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-primary-sport btn-lg">
                    <i class="bi bi-speedometer2 me-2"></i> لوحة التحكم
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary-sport btn-lg">
                    <i class="bi bi-rocket-takeoff me-2"></i> سجل مجاناً الآن
                </a>
            @endauth
        </div>
        <div class="hero-shape" style="width:300px;height:300px;top:-100px;left:-100px;"></div>
        <div class="hero-shape" style="width:200px;height:200px;bottom:-80px;right:-80px;"></div>
    </section>
</div>
