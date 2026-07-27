<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.index') }}" class="text-decoration-none" style="color:var(--primary);">المباريات</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.edit', $match) }}" class="text-decoration-none" style="color:var(--primary);">{{ $match->team1->name ?? '?' }} vs {{ $match->team2->name ?? '?' }}</a></li>
            <li class="breadcrumb-item active">الإحصائيات</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-bar-chart-fill text-gold"></i> إحصائيات المباراة</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">
                {{ $match->team1->name ?? '?' }}
                <span class="text-gold fw-bold mx-1">vs</span>
                {{ $match->team2->name ?? '?' }}
            </p>
        </div>
        <a href="{{ route('admin.matches.edit', $match) }}" class="btn btn-outline-secondary" style="border-radius:8px;">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>

    {{-- Stats Comparison Bars --}}
    <div class="card border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold mb-0"><i class="bi bi-graph-up text-gold"></i> المقارنة البصرية</h6>
        </div>
        <div class="card-body">
            @php
                $comparisonStats = [
                    'possession' => ['label' => 'الاستحواذ', 'max' => 100, 'suffix' => '%', 'isPercent' => true],
                    'shots_total' => ['label' => 'التسديدات', 'max' => null],
                    'shots_on_target' => ['label' => 'التسديدات على المرمى', 'max' => null],
                    'corners' => ['label' => 'الركنيات', 'max' => null],
                    'fouls' => ['label' => 'الأخطاء', 'max' => null],
                    'offsides' => ['label' => 'التسلل', 'max' => null],
                    'yellow_cards' => ['label' => 'البطاقات الصفراء', 'max' => null],
                    'red_cards' => ['label' => 'البطاقات الحمراء', 'max' => null],
                    'passes_total' => ['label' => 'التمريرات', 'max' => null],
                    'passes_accurate' => ['label' => 'التمريرات الدقيقة', 'max' => null],
                    'tackles' => ['label' => 'التدخلات', 'max' => null],
                    'saves' => ['label' => 'التصديات', 'max' => null],
                ];
            @endphp

            @forelse($comparisonStats as $key => $stat)
                @php
                    $t1Val = $team1Stats->$key ?? 0;
                    $t2Val = $team2Stats->$key ?? 0;
                    if ($stat['isPercent'] ?? false) {
                        $maxVal = 100;
                    } else {
                        $maxVal = max($t1Val, $t2Val, 1);
                    }
                    $t1Width = round(($t1Val / $maxVal) * 100);
                    $t2Width = round(($t2Val / $maxVal) * 100);
                    $suffix = $stat['suffix'] ?? '';
                @endphp
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-1">
                        <div class="text-end" style="width:80px;">
                            <strong style="font-size:0.95rem;">{{ $t1Val }}{{ $suffix }}</strong>
                        </div>
                        <div class="flex-grow-1 px-3">
                            <div class="d-flex align-items-center gap-1" style="direction:ltr;">
                                <div class="flex-grow-1">
                                    <div class="progress" style="height:10px;border-radius:5px;">
                                        <div class="progress-bar bg-primary" style="width:{{ $t1Width }}%;border-radius:5px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center flex-shrink-0" style="width:140px;">
                            <small class="text-muted fw-bold" style="font-size:0.8rem;">{{ $stat['label'] }}</small>
                        </div>
                        <div class="flex-grow-1 px-3">
                            <div class="d-flex align-items-center gap-1" style="direction:ltr;">
                                <div class="flex-grow-1">
                                    <div class="progress" style="height:10px;border-radius:5px;">
                                        <div class="progress-bar bg-warning" style="width:{{ $t2Width }}%;border-radius:5px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-start" style="width:80px;">
                            <strong style="font-size:0.95rem;">{{ $t2Val }}{{ $suffix }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state py-3 text-center">
                    <i class="bi bi-bar-chart d-block text-muted" style="font-size:2rem;"></i>
                    <small class="text-muted">لا توجد إحصائيات متاحة</small>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Editing Form --}}
    <div class="card border-0">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square text-gold"></i> تعديل الإحصائيات</h6>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Team Tabs --}}
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <button class="nav-link {{ $activeTeam == 1 ? 'active' : '' }}" wire:click="switchTeam(1)">
                        {{ $match->team1->name }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTeam == 2 ? 'active' : '' }}" wire:click="switchTeam(2)">
                        {{ $match->team2->name }}
                    </button>
                </li>
            </ul>

            <form wire:submit="saveStats">
                <div class="row g-3">
                    {{-- Possession --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الاستحواذ (%)</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.possession' : 'statsForm2.possession' }}"
                               min="0" max="100" step="0.1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الاستحواذ (%)</label>
                        @php
                            $otherPossession = $activeTeam == 1
                                ? ($statsForm2['possession'] ?? 50)
                                : ($statsForm['possession'] ?? 50);
                            $currentPossession = $activeTeam == 1
                                ? ($statsForm['possession'] ?? 50)
                                : ($statsForm2['possession'] ?? 50);
                        @endphp
                        <input type="number" class="form-control"
                               value="{{ 100 - ($activeTeam == 1 ? $otherPossession : $currentPossession) }}" disabled>
                        <small class="text-muted" style="font-size:0.75rem;">
                            محسب تلقائياً ({{ $activeTeam == 1 ? ($match->team2->name ?? 'الفريق الثاني') : ($match->team1->name ?? 'الفريق الأول') }})
                        </small>
                    </div>

                    {{-- Shots --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">التسديدات</small></div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">التسديدات الكلية</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.shots_total' : 'statsForm2.shots_total' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">التسديدات على المرمى</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.shots_on_target' : 'statsForm2.shots_on_target' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">التسديدات خارج المرمى</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.shots_off_target' : 'statsForm2.shots_off_target' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">التسديدات المحجوبة</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.blocked_shots' : 'statsForm2.blocked_shots' }}" min="0">
                    </div>

                    {{-- Set Pieces --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">الركنيات والمخالفات</small></div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">الركنيات</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.corners' : 'statsForm2.corners' }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">الأخطاء</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.fouls' : 'statsForm2.fouls' }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">التسلل</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.offsides' : 'statsForm2.offsides' }}" min="0">
                    </div>

                    {{-- Cards --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">البطاقات</small></div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">البطاقات الصفراء</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.yellow_cards' : 'statsForm2.yellow_cards' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">البطاقات الحمراء</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.red_cards' : 'statsForm2.red_cards' }}" min="0">
                    </div>

                    {{-- Passing --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">التمريرات</small></div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">التمريرات الكلية</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.passes_total' : 'statsForm2.passes_total' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">التمريرات الدقيقة</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.passes_accurate' : 'statsForm2.passes_accurate' }}" min="0">
                    </div>

                    {{-- Defense --}}
                    <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">الدفاع والتصدي</small></div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">التدخلات</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.tackles' : 'statsForm2.tackles' }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">التصديات</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.saves' : 'statsForm2.saves' }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">إصابة العارضة</label>
                        <input type="number" class="form-control"
                               wire:model="{{ $activeTeam == 1 ? 'statsForm.hit_woodwork' : 'statsForm2.hit_woodwork' }}" min="0">
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <small class="text-muted" style="font-size:0.8rem;">
                        <i class="bi bi-info-circle"></i>
                        إحصائيات {{ $activeTeam == 1 ? ($match->team1->name ?? 'الفريق الأول') : ($match->team2->name ?? 'الفريق الثاني') }}
                    </small>
                    <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveStats"><i class="bi bi-check-lg"></i> حفظ الإحصائيات</span>
                        <span wire:loading wire:target="saveStats"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Section --}}
    <div class="card border-0 mt-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold mb-0"><i class="bi bi-clipboard-data text-gold"></i> ملخص المباراة</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <div class="border rounded-3 p-3" style="background:#f8f9fa;">
                        <h3 class="fw-bold text-primary mb-1">{{ $match->score_team1 ?? 0 }}</h3>
                        <small class="text-muted fw-bold">{{ $match->team1->name ?? 'الفريق الأول' }}</small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded-3 p-3" style="background:#f8f9fa;">
                        <small class="text-muted d-block mb-1">النتيجة النهائية</small>
                        <h3 class="fw-bold text-gold mb-0">{{ $match->score_team1 ?? 0 }} - {{ $match->score_team2 ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded-3 p-3" style="background:#f8f9fa;">
                        <h3 class="fw-bold text-warning mb-1">{{ $match->score_team2 ?? 0 }}</h3>
                        <small class="text-muted fw-bold">{{ $match->team2->name ?? 'الفريق الثاني' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
