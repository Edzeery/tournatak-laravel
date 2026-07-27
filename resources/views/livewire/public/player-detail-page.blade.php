<div>
    <section class="hero-sports text-white" style="min-height:280px;">
        <div class="container hero-content">
            <div class="d-flex align-items-center gap-4" style="position:relative;z-index:2;">
                @if($player->image)
                    <img src="{{ asset('uploads/players/' . $player->image) }}" alt="{{ $player->name ?? '' }}" class="rounded-circle" width="110" height="110" style="object-fit:cover;border:4px solid rgba(255,255,255,0.3);">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-gold text-dark fw-bold" style="width:110px;height:110px;font-size:2.5rem;">
                        {{ mb_substr($player->name ?? 'ل', 0, 1) }}
                    </div>
                @endif
                <div>
                    @if($player->number)
                        <span class="badge bg-gold text-dark mb-1" style="font-size:0.9rem;">#{{ $player->number }}</span>
                    @endif
                    <h1 class="fw-bold mb-1" style="font-size:2rem;">{{ $player->name ?? 'لاعب' }}</h1>
                    <p style="color:rgba(255,255,255,0.7);margin:0;">
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
                        <span class="badge bg-warning text-dark mt-1" style="font-size:0.75rem;">قائد الفريق (C)</span>
                    @endif
                </div>
            </div>
        </div>
        <div style="position:absolute;bottom:0;left:0;right:0;height:80px;background:linear-gradient(to top, #f5f6fa, transparent);"></div>
    </section>

    <div class="container py-5" style="margin-top:-20px;">
        <div class="row g-4">
            {{-- Main Info --}}
            <div class="col-lg-8">
                {{-- Bio --}}
                @if($player->bio)
                    <div class="card border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2" style="color:var(--dark);">
                                <i class="bi bi-person-lines-fill text-gold"></i> نبذة
                            </h6>
                            <p style="font-size:0.9rem;line-height:1.7;color:#555;">{{ $player->bio }}</p>
                        </div>
                    </div>
                @endif

                {{-- Stats Summary --}}
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="card border-0 text-center" style="border-radius:12px;">
                            <div class="card-body py-3">
                                <div class="fw-bold" style="font-size:1.8rem;color:var(--primary);">{{ $totalGoals }}</div>
                                <small class="text-muted" style="font-size:0.8rem;">هدف</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 text-center" style="border-radius:12px;">
                            <div class="card-body py-3">
                                <div class="fw-bold" style="font-size:1.8rem;color:var(--primary);">{{ $player->lineups()->count() }}</div>
                                <small class="text-muted" style="font-size:0.8rem;">مباراة</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 text-center" style="border-radius:12px;">
                            <div class="card-body py-3">
                                <div class="fw-bold" style="font-size:1.8rem;color:var(--primary);">{{ $player->lineups()->where('is_starter', true)->count() }}</div>
                                <small class="text-muted" style="font-size:0.8rem;"> titular</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Goals --}}
                @if($goals->count())
                    <div class="card border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color:var(--dark);">
                                <i class="bi bi-circle-fill text-gold" style="font-size:0.7rem;"></i> الأهداف
                            </h6>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="font-size:0.8rem;">المباراة</th>
                                            <th style="font-size:0.8rem;">التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($goals as $goal)
                                            <tr>
                                                <td style="font-size:0.85rem;">
                                                    {{ $goal->match->team1->name ?? '?' }} vs {{ $goal->match->team2->name ?? '?' }}
                                                </td>
                                                <td style="font-size:0.8rem;color:#94a3b8;">
                                                    {{ $goal->match->match_date?->format('d/m/Y') ?? '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Season Stats --}}
                @if($seasonStats->count())
                    <div class="card border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color:var(--dark);">
                                <i class="bi bi-bar-chart-line text-gold"></i> إحصائيات المواسم
                            </h6>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="font-size:0.8rem;">البطولة</th>
                                            <th style="font-size:0.8rem;text-align:center;">م</th>
                                            <th style="font-size:0.8rem;text-align:center;">هدف</th>
                                            <th style="font-size:0.8rem;text-align:center;">تمريرة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($seasonStats as $stat)
                                            <tr>
                                                <td style="font-size:0.85rem;">{{ $stat->competition->name ?? '—' }}</td>
                                                <td class="text-center" style="font-size:0.85rem;">{{ $stat->matches_played ?? 0 }}</td>
                                                <td class="text-center fw-bold" style="font-size:0.85rem;color:var(--primary);">{{ $stat->goals ?? 0 }}</td>
                                                <td class="text-center" style="font-size:0.85rem;">{{ $stat->assists ?? 0 }}</td>
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
                        <h6 class="fw-bold mb-3" style="color:var(--dark);">
                            <i class="bi bi-info-circle text-gold"></i> معلومات اللاعب
                        </h6>
                        <div class="d-flex flex-column gap-2">
                            @if($player->position)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted" style="font-size:0.85rem;">المركز</span>
                                    <span class="fw-bold" style="font-size:0.85rem;">{{ $player->position->name }}</span>
                                </div>
                            @endif
                            @if($player->date_of_birth)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted" style="font-size:0.85rem;">تاريخ الميلاد</span>
                                    <span class="fw-bold" style="font-size:0.85rem;">{{ $player->date_of_birth->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($player->nationality)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted" style="font-size:0.85rem;">الجنسية</span>
                                    <span class="fw-bold" style="font-size:0.85rem;">{{ $player->nationality }}</span>
                                </div>
                            @endif
                            @if($player->height)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted" style="font-size:0.85rem;">الطول</span>
                                    <span class="fw-bold" style="font-size:0.85rem;">{{ $player->height }} سم</span>
                                </div>
                            @endif
                            @if($player->weight)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted" style="font-size:0.85rem;">الوزن</span>
                                    <span class="fw-bold" style="font-size:0.85rem;">{{ $player->weight }} كغ</span>
                                </div>
                            @endif
                            @if($player->foot)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted" style="font-size:0.85rem;">القدم</span>
                                    <span class="fw-bold" style="font-size:0.85rem;">{{ $player->foot === 'right' ? 'يمين' : ($player->foot === 'left' ? 'يسار' : 'كلتاهما') }}</span>
                                </div>
                            @endif
                            @if($player->sport_type)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted" style="font-size:0.85rem;">نوع الرياضة</span>
                                    <span class="fw-bold" style="font-size:0.85rem;">{{ $player->sport_type === 'football' ? 'كرة قدم' : 'فوتسال' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
