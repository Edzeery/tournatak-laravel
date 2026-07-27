<div>
    <section class="hero-sports text-white" style="min-height:250px;">
        <div class="container hero-content">
            <div class="d-flex align-items-center gap-4" style="position:relative;z-index:2;">
                @if($team->logo)
                    <img src="{{ asset('uploads/teams/' . $team->logo) }}" alt="{{ $team->name }}" class="rounded-circle" width="100" height="100" style="object-fit:cover;border:4px solid rgba(255,255,255,0.3);">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-gold text-dark fw-bold" style="width:100px;height:100px;font-size:2.5rem;">
                        {{ mb_substr($team->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="fw-bold mb-1" style="font-size:2rem;">{{ $team->name }}</h1>
                    @if($team->captain)
                        <p style="color:rgba(255,255,255,0.7);margin:0;">
                            <i class="bi bi-person-fill"></i> القائد: {{ $team->captain->name }}
                        </p>
                    @endif
                    <span class="badge bg-gold text-dark mt-2" style="font-size:0.85rem;">
                        <i class="bi bi-star-fill"></i> {{ $team->points ?? 0 }} نقطة
                    </span>
                </div>
            </div>
        </div>
        <div style="position:absolute;bottom:0;left:0;right:0;height:80px;background:linear-gradient(to top, #f5f6fa, transparent);"></div>
    </section>

    <div class="container py-5" style="margin-top:-20px;">
        <div class="row g-4">
            {{-- Players --}}
            <div class="col-lg-8">
                <div class="card border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3" style="color:var(--dark);">
                            <i class="bi bi-people-fill text-gold"></i> اللاعبون
                            <span class="badge bg-secondary rounded-pill ms-2" style="font-size:0.75rem;">{{ $players->count() }}</span>
                        </h5>
                        @if($players->count())
                            <div class="row g-3">
                                @foreach($players as $player)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ route('players.show', $player->id) }}" class="text-decoration-none">
                                            <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background:rgba(0,0,0,0.02);transition:all 0.2s;">
                                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width:44px;height:44px;min-width:44px;font-size:0.85rem;font-weight:700;color:var(--primary);">
                                                    {{ $player->number ?? '?' }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold" style="font-size:0.85rem;color:var(--dark);">{{ $player->name ?? 'لاعب' }}</div>
                                                    <small class="text-muted" style="font-size:0.75rem;">
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
                                <i class="bi bi-people d-block mb-2" style="font-size:2rem;"></i>
                                <p style="font-size:0.9rem;">لا يوجد لاعبون مسجلون بعد</p>
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
                        <h6 class="fw-bold mb-3" style="color:var(--dark);">
                            <i class="bi bi-person-workspace text-gold"></i> الطاقم الفني
                        </h6>
                        @if($staff->count())
                            @foreach($staff as $member)
                                <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-light" style="width:38px;height:38px;min-width:38px;">
                                        <i class="bi bi-award text-gold" style="font-size:0.9rem;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size:0.85rem;">{{ $member->user->name ?? '—' }}</div>
                                        <small class="text-muted" style="font-size:0.75rem;">{{ \App\Models\TeamStaff::STAFF_ROLES[$member->staff_role] ?? $member->staff_role }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted">
                                <small style="font-size:0.85rem;">لم يتم تعيين طاقم فني بعد</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Formations --}}
                <div class="card border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3" style="color:var(--dark);">
                            <i class="bi bi-diagram-3 text-gold"></i> التشكيلات
                        </h6>
                        @if($formations->count())
                            @foreach($formations->take(4) as $formation)
                                <div class="d-flex align-items-center justify-content-between {{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
                                    <div>
                                        <div class="fw-bold" style="font-size:0.85rem;">{{ $formation->name }}</div>
                                        <small class="text-muted" style="font-size:0.75rem;">{{ $formation->formation_code }}</small>
                                    </div>
                                    @if($formation->is_default)
                                        <span class="badge bg-gold text-dark" style="font-size:0.7rem;">افتراضي</span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted">
                                <small style="font-size:0.85rem;">لم يتم إنشاء تشكيلات بعد</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
