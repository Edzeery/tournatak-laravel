@php $isRtl = isRtl(); @endphp
<div>
    {{-- Welcome Hero --}}
    <div class="card border-0 mb-4 animate-fade-in" style="background: var(--gradient-hero);overflow:hidden;position:relative;">
        <div style="position:absolute;top:-50px;{{ isRtl() ? 'right' : 'left' }}:-50px;width:300px;height:300px;background:radial-gradient(circle,rgba(255,193,7,0.1) 0%,transparent 70%);border-radius:50%;"></div>
        <div class="card-body p-4 position-relative" style="z-index:2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold animate-scale-in" style="width:64px;height:64px;font-size:1.5rem;">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-white fw-bold mb-0" style="font-size:1.4rem;">
                                {{ $isAr ? 'مرحباً بك' : 'Welcome back' }}, {{ $user->name }}!
                            </h4>
                            <p style="color:rgba(255,255,255,0.5);margin:0;font-size:0.9rem;">
                                {{ $isAr ? 'إليك نظرة عامة على حسابك ونشاطك' : 'Here\'s an overview of your account and activity' }}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('user.profile') }}" class="btn btn-primary-sport btn-sm">
                            <i class="bi bi-person-gear me-1"></i> {{ $isAr ? 'تعديل الملف' : 'Edit Profile' }}
                        </a>
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-sport btn-sm">
                            <i class="bi bi-shield me-1"></i> {{ $isAr ? 'تصفح الفرق' : 'Browse Teams' }}
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:100px;height:100px;background:rgba(255,193,7,0.1);border:3px solid rgba(255,193,7,0.3);">
                        <i class="bi bi-trophy-fill text-gold" style="font-size:2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4 stagger-children">
        <div class="col-6 col-md-3">
            <div class="stat-card card-hover">
                <div class="stat-icon bg-gold bg-opacity-10 text-gold"><i class="bi bi-shield-fill"></i></div>
                <div class="stat-number count-animate" style="font-size:1.8rem;">{{ $stats['teams'] }}</div>
                <div class="stat-label">{{ $isAr ? 'فريق' : 'Teams' }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card card-hover">
                <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;"><i class="bi bi-trophy-fill"></i></div>
                <div class="stat-number count-animate" style="font-size:1.8rem;">{{ $stats['competitions'] }}</div>
                <div class="stat-label">{{ $isAr ? 'بطولة' : 'Competitions' }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card card-hover">
                <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="bi bi-circle-fill"></i></div>
                <div class="stat-number count-animate" style="font-size:1.8rem;">{{ $stats['goals'] }}</div>
                <div class="stat-label">{{ $isAr ? 'هدف' : 'Goals' }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card card-hover">
                <div class="stat-icon" style="background:rgba(168,85,247,0.1);color:#a855f7;"><i class="bi bi-calendar-event-fill"></i></div>
                <div class="stat-number count-animate" style="font-size:1.8rem;">{{ $stats['matches'] }}</div>
                <div class="stat-label">{{ $isAr ? 'مباراة' : 'Matches' }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="card border-0 animate-fade-in-up animate-delay-1">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--dark);">
                        <i class="bi bi-person-badge text-gold"></i> {{ $isAr ? 'معلومات الحساب' : 'Account Info' }}
                    </h6>
                    <div class="text-center mb-4">
                        <div class="bg-gold text-dark rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-2 animate-scale-in" style="width:72px;height:72px;font-size:1.8rem;">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <h5 class="fw-bold mb-0" style="color:var(--dark);">{{ $user->name }}</h5>
                        <small style="color:#94a3b8;">@{{ $user->username }}</small>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background:#f8f9fa;">
                            <small class="text-muted"><i class="bi bi-envelope me-1"></i> {{ $isAr ? 'البريد' : 'Email' }}</small>
                            <small class="fw-bold" style="font-size:0.8rem;">{{ $user->email }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background:#f8f9fa;">
                            <small class="text-muted"><i class="bi bi-shield me-1"></i> {{ $isAr ? 'الدور' : 'Role' }}</small>
                            <span class="badge bg-warning-subtle text-warning fw-bold" style="font-size:0.75rem;">{{ $user->role ?? 'user' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background:#f8f9fa;">
                            <small class="text-muted"><i class="bi bi-calendar me-1"></i> {{ $isAr ? 'التسجيل' : 'Joined' }}</small>
                            <small class="fw-bold" style="font-size:0.8rem;">{{ $user->created_at->format('d/m/Y') }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background:#f8f9fa;">
                            <small class="text-muted"><i class="bi bi-check-circle me-1"></i> {{ $isAr ? 'الحالة' : 'Status' }}</small>
                            @if($user->is_verified)
                                <span class="badge" style="background:rgba(16,185,129,0.1);color:#10b981;font-size:0.75rem;">{{ $isAr ? 'موثق' : 'Verified' }}</span>
                            @else
                                <span class="badge bg-secondary" style="font-size:0.75rem;">{{ $isAr ? 'غير موثق' : 'Unverified' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8">
            {{-- Recent Matches --}}
            <div class="card border-0 mb-4 animate-fade-in-up animate-delay-2">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--dark);">
                        <i class="bi bi-calendar-event text-gold"></i> {{ $isAr ? 'آخر المباريات' : 'Recent Matches' }}
                    </h6>
                    @if($recentMatches->count())
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="font-size:0.8rem;">{{ $isAr ? 'الفريق ١' : 'Team 1' }}</th>
                                        <th style="font-size:0.8rem;text-align:center;">{{ $isAr ? 'النتيجة' : 'Score' }}</th>
                                        <th style="font-size:0.8rem;">{{ $isAr ? 'الفريق ٢' : 'Team 2' }}</th>
                                        <th style="font-size:0.8rem;">{{ $isAr ? 'التاريخ' : 'Date' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMatches as $match)
                                        <tr>
                                            <td class="fw-bold" style="font-size:0.85rem;">{{ $match->team1->name ?? '—' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-dark rounded-pill px-3" style="font-size:0.85rem;">
                                                    {{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}
                                                </span>
                                            </td>
                                            <td class="fw-bold" style="font-size:0.85rem;">{{ $match->team2->name ?? '—' }}</td>
                                            <td style="font-size:0.8rem;color:#94a3b8;">{{ $match->match_date?->format('d/m') ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x d-block mb-2" style="font-size:2rem;color:#cbd5e1;"></i>
                            <p style="color:#94a3b8;font-size:0.9rem;">{{ $isAr ? 'لا توجد مباريات حتى الآن' : 'No matches yet' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card border-0 animate-fade-in-up animate-delay-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--dark);">
                        <i class="bi bi-lightning-fill text-gold"></i> {{ $isAr ? 'إجراءات سريعة' : 'Quick Actions' }}
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('user.profile') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                            <i class="bi bi-person-gear"></i> {{ $isAr ? 'الملف الشخصي' : 'Profile' }}
                        </a>
                        <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-success" style="border-radius:8px;">
                            <i class="bi bi-shield"></i> {{ $isAr ? 'تصفح الفرق' : 'Browse Teams' }}
                        </a>
                        <a href="{{ route('competitions.index') }}" class="btn btn-sm btn-outline-warning" style="border-radius:8px;">
                            <i class="bi bi-trophy"></i> {{ $isAr ? 'البطولات' : 'Competitions' }}
                        </a>
                        <a href="{{ route('players.index') }}" class="btn btn-sm btn-outline-info" style="border-radius:8px;">
                            <i class="bi bi-people"></i> {{ $isAr ? 'اللاعبون' : 'Players' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
