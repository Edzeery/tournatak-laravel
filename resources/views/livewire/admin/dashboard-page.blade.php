<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);">
                <i class="bi bi-grid-1x2-fill text-gold"></i> لوحة التحكم
            </h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">نظرة عامة على إحصائيات المنصة</p>
        </div>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">الرئيسية</li>
        </ol>
    </nav>

    {{-- Main Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['users'] }}</div>
                <div class="stat-label">مستخدم</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-gold bg-opacity-10 text-gold">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['competitions'] }}</div>
                <div class="stat-label">بطولة</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10" style="color:#16a34a;">
                    <i class="bi bi-shield-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['teams'] }}</div>
                <div class="stat-label">فريق</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(168,85,247,0.1);color:#a855f7;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['players'] }}</div>
                <div class="stat-label">لاعب</div>
            </div>
        </div>
    </div>

    {{-- Match Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b;">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['matches'] }}</div>
                <div class="stat-label">مباراة</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(239,68,68,0.1);color:#ef4444;">
                    <i class="bi bi-circle-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['goals'] }}</div>
                <div class="stat-label">هدف</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(6,182,212,0.1);color:#06b6d4;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['staff'] }}</div>
                <div class="stat-label">عضو طاقم</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(220,38,38,0.1);color:#dc2626;">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div class="stat-number">{{ $stats['injuries'] }}</div>
                <div class="stat-label">إصابة نشطة</div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card border-0 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3" style="color:var(--dark);">
                <i class="bi bi-lightning-fill text-gold"></i> إجراءات سريعة
            </h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.teams.create') }}" class="btn btn-sm btn-outline-success" style="border-radius:8px;">
                    <i class="bi bi-plus-lg"></i> فريق جديد
                </a>
                <a href="{{ route('admin.players.create') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                    <i class="bi bi-plus-lg"></i> لاعب جديد
                </a>
                <a href="{{ route('admin.matches.create') }}" class="btn btn-sm btn-outline-warning" style="border-radius:8px;">
                    <i class="bi bi-plus-lg"></i> مباراة جديدة
                </a>
                <a href="{{ route('admin.competitions.create') }}" class="btn btn-sm btn-outline-info" style="border-radius:8px;">
                    <i class="bi bi-plus-lg"></i> بطولة جديدة
                </a>
                <a href="{{ route('admin.positions.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                    <i class="bi bi-geo-alt"></i> إدارة المراكز
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Matches --}}
        <div class="col-lg-8">
            @if($recentMatches->count())
                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3" style="color:var(--dark);">
                            <i class="bi bi-calendar-event text-gold"></i> آخر المباريات
                        </h6>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="font-size:0.8rem;">الفريق ١</th>
                                        <th style="font-size:0.8rem;text-align:center;">النتيجة</th>
                                        <th style="font-size:0.8rem;">الفريق ٢</th>
                                        <th style="font-size:0.8rem;">التاريخ</th>
                                        <th style="font-size:0.8rem;">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMatches as $match)
                                        <tr wire:key="rm-{{ $match->id }}">
                                            <td class="fw-bold" style="font-size:0.85rem;">{{ $match->team1->name ?? '—' }}</td>
                                            <td class="text-center">
                                                @if($match->status === 'completed')
                                                    <span class="badge bg-dark rounded-pill px-3" style="font-size:0.85rem;">
                                                        {{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary rounded-pill" style="font-size:0.75rem;">
                                                        {{ $match->status === 'scheduled' ? 'مجدول' : 'جاري' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="fw-bold" style="font-size:0.85rem;">{{ $match->team2->name ?? '—' }}</td>
                                            <td style="font-size:0.8rem;color:#94a3b8;">{{ $match->match_date?->format('d/m') ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('admin.matches.lineup', $match) }}" class="btn btn-sm btn-outline-success" style="border-radius:6px;padding:2px 8px;font-size:0.7rem;" title="التشكيلة">
                                                    <i class="bi bi-people-fill"></i>
                                                </a>
                                                <a href="{{ route('admin.matches.events', $match) }}" class="btn btn-sm btn-outline-warning" style="border-radius:6px;padding:2px 8px;font-size:0.7rem;" title="الأحداث">
                                                    <i class="bi bi-clock-history"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Top Scorers --}}
        <div class="col-lg-4">
            @if($topScorers->count())
                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3" style="color:var(--dark);">
                            <i class="bi bi-award-fill text-gold"></i> الهدافون
                        </h6>
                        @foreach($topScorers as $idx => $scorer)
                            <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
                                <span class="badge bg-{{ $idx === 0 ? 'warning text-dark' : ($idx === 1 ? 'secondary' : 'dark') }} rounded-circle" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:0.85rem;">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-bold" style="font-size:0.85rem;">{{ $scorer->player->name ?? '—' }}</div>
                                    <small class="text-muted" style="font-size:0.75rem;">{{ $scorer->player->team->name ?? '' }}</small>
                                </div>
                                <span class="badge bg-danger rounded-pill" style="font-size:0.8rem;">{{ $scorer->goals }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="card border-0 mt-4" wire:loading.opacity>
        <div class="card-body">
            <h6 class="fw-bold mb-3" style="color:var(--dark);">
                <i class="bi bi-clock-history text-gold"></i> آخر الأنشطة
            </h6>
            @if($activities->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="font-size:0.8rem;">#</th>
                                <th style="font-size:0.8rem;">النشاط</th>
                                <th style="font-size:0.8rem;">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr wire:key="{{ $activity->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold" style="font-size:0.9rem;">{{ $activity->description }}</td>
                                    <td style="color:#94a3b8;font-size:0.85rem;">{{ $activity->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state py-3">
                    <i class="bi bi-clock-history d-block" style="font-size:2.5rem;"></i>
                    <h5>لا توجد أنشطة بعد</h5>
                </div>
            @endif
        </div>
    </div>
</div>
