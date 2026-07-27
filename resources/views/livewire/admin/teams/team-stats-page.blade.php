<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="text-decoration-none" style="color:var(--primary);">الفرق</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.edit', $team) }}" class="text-decoration-none" style="color:var(--primary);">{{ $team->name }}</a></li>
            <li class="breadcrumb-item active">الإحصائيات</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-bar-chart-line-fill text-gold"></i> الإحصائيات</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">{{ $team->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-warning px-3" wire:click="openModal" style="border-radius:8px;">
                <i class="bi bi-plus-lg"></i> إضافة إحصائية
            </button>
            <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-outline-secondary" style="border-radius:8px;">
                <i class="bi bi-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($availableSeasons->isNotEmpty())
        <div class="card border-0 mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">الموسم</label>
                        <select class="form-select" wire:model.live="selectedSeason">
                            @foreach($availableSeasons as $season)
                                <option value="{{ $season }}">{{ $season }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        @php
            $totalMatches = $this->totalMatchesPlayed;
            $totalWins = $this->totalWins;
            $totalDraws = $this->totalDraws;
            $totalLosses = $this->totalLosses;
            $totalGoalsFor = $this->totalGoalsFor;
            $totalPoints = $this->totalPoints;
        @endphp
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <i class="bi bi-calendar-event text-gold" style="font-size:1.3rem;"></i>
                    <div class="fw-bold mt-1" style="font-size:1.8rem;">{{ $totalMatches }}</div>
                    <small class="text-muted fw-bold">المباريات</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <i class="bi bi-trophy text-gold" style="font-size:1.3rem;"></i>
                    <div class="fw-bold mt-1" style="font-size:1.8rem;color:#198754;">{{ $totalWins }}</div>
                    <small class="text-muted fw-bold">الانتصارات</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <i class="bi bi-handshake text-gold" style="font-size:1.3rem;"></i>
                    <div class="fw-bold mt-1" style="font-size:1.8rem;color:#ffc107;">{{ $totalDraws }}</div>
                    <small class="text-muted fw-bold">التعادلات</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <i class="bi bi-x-circle text-gold" style="font-size:1.3rem;"></i>
                    <div class="fw-bold mt-1" style="font-size:1.8rem;color:#dc3545;">{{ $totalLosses }}</div>
                    <small class="text-muted fw-bold">الخسارات</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <i class="bi bi-bullseye text-gold" style="font-size:1.3rem;"></i>
                    <div class="fw-bold mt-1" style="font-size:1.8rem;color:#0d6efd;">{{ $totalGoalsFor }}</div>
                    <small class="text-muted fw-bold">الأهداف</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <i class="bi bi-star-fill text-gold" style="font-size:1.3rem;"></i>
                    <div class="fw-bold mt-1" style="font-size:1.8rem;color:#0dcaf0;">{{ $totalPoints }}</div>
                    <small class="text-muted fw-bold">النقاط</small>
                </div>
            </div>
        </div>
    </div>

    @if($totalMatches > 0)
        <div class="card border-0 mb-4">
            <div class="card-header border-0 fw-bold bg-transparent pt-3">
                <i class="bi bi-pie-chart text-gold"></i> نسب النتائج
            </div>
            <div class="card-body">
                @php
                    $winPct = $totalMatches > 0 ? round(($totalWins / $totalMatches) * 100) : 0;
                    $drawPct = $totalMatches > 0 ? round(($totalDraws / $totalMatches) * 100) : 0;
                    $lossPct = $totalMatches > 0 ? round(($totalLosses / $totalMatches) * 100) : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold" style="font-size:0.85rem;">الانتصارات</span>
                        <span class="fw-bold" style="font-size:0.85rem;color:#198754;">{{ $winPct }}%</span>
                    </div>
                    <div class="progress" style="height:10px;border-radius:5px;">
                        <div class="progress-bar bg-success" style="width:{{ $winPct }}%;border-radius:5px;"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold" style="font-size:0.85rem;">التعادلات</span>
                        <span class="fw-bold" style="font-size:0.85rem;color:#ffc107;">{{ $drawPct }}%</span>
                    </div>
                    <div class="progress" style="height:10px;border-radius:5px;">
                        <div class="progress-bar bg-warning" style="width:{{ $drawPct }}%;border-radius:5px;"></div>
                    </div>
                </div>
                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold" style="font-size:0.85rem;">الخسارات</span>
                        <span class="fw-bold" style="font-size:0.85rem;color:#dc3545;">{{ $lossPct }}%</span>
                    </div>
                    <div class="progress" style="height:10px;border-radius:5px;">
                        <div class="progress-bar bg-danger" style="width:{{ $lossPct }}%;border-radius:5px;"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0" wire:loading.opacity>
        <div class="card-header border-0 fw-bold bg-transparent pt-3">
            <i class="bi bi-table text-gold"></i> تفاصيل الإحصائيات حسب البطولة
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>البطولة</th>
                            <th>الموسم</th>
                            <th>المباريات</th>
                            <th>انتصارات</th>
                            <th>تعادلات</th>
                            <th>خسارات</th>
                            <th>الأهداف</th>
                            <th>النقاط</th>
                            <th>الاستحواذ</th>
                            <th>بطاقات صفراء</th>
                            <th>بطاقات حمراء</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($seasonStats as $stat)
                            <tr wire:key="{{ $stat->id }}">
                                <td class="fw-bold">{{ $stat->competition->name ?? '—' }}</td>
                                <td style="font-size:0.85rem;color:#94a3b8;">{{ $stat->season_year }}</td>
                                <td>{{ $stat->matches_played }}</td>
                                <td><span class="fw-bold" style="color:#198754;">{{ $stat->wins }}</span></td>
                                <td><span class="fw-bold" style="color:#ffc107;">{{ $stat->draws }}</span></td>
                                <td><span class="fw-bold" style="color:#dc3545;">{{ $stat->losses }}</span></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-bold">
                                        {{ $stat->goals_for }} - {{ $stat->goals_against }}
                                    </span>
                                </td>
                                <td><span class="badge bg-warning text-dark fw-bold">{{ $stat->points }}</span></td>
                                <td style="font-size:0.85rem;">{{ $stat->possession_avg ?? '—' }}%</td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning fw-bold">{{ $stat->yellow_cards ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger fw-bold">{{ $stat->red_cards ?? 0 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary" wire:click="editStat({{ $stat->id }})" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" wire:click="deleteStat({{ $stat->id }})" wire:confirm="هل أنت متأكد من حذف هذه الإحصائية؟" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12">
                                    <div class="empty-state py-3">
                                        <i class="bi bi-bar-chart d-block" style="font-size:2.5rem;"></i>
                                        <h5>لا توجد إحصائيات</h5>
                                        <p class="text-muted">لم يتم تسجيل أي إحصائيات بعد</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="border-radius:12px;">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-bar-chart-line text-gold"></i>
                            {{ $editingStatId ? 'تعديل إحصائية' : 'إضافة إحصائية جديدة' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">البطولة <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="statForm.competition_id">
                                    <option value="">-- اختر البطولة --</option>
                                    @foreach($availableCompetitions as $comp)
                                        <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                                    @endforeach
                                </select>
                                @error('statForm.competition_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">الموسم <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" wire:model="statForm.season_year" min="2000" max="2100">
                                @error('statForm.season_year') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">المباريات والنتائج</small></div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">المباريات</label>
                                <input type="number" class="form-control" wire:model="statForm.matches_played" min="0">
                                @error('statForm.matches_played') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">الانتصارات</label>
                                <input type="number" class="form-control" wire:model="statForm.wins" min="0">
                                @error('statForm.wins') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">التعادلات</label>
                                <input type="number" class="form-control" wire:model="statForm.draws" min="0">
                                @error('statForm.draws') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">الخسارات</label>
                                <input type="number" class="form-control" wire:model="statForm.losses" min="0">
                                @error('statForm.losses') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">الأهداف المسجلة</label>
                                <input type="number" class="form-control" wire:model="statForm.goals_for" min="0">
                                @error('statForm.goals_for') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">الأهداف المستلمة</label>
                                <input type="number" class="form-control" wire:model="statForm.goals_against" min="0">
                                @error('statForm.goals_against') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">الشباك النظيفة</label>
                                <input type="number" class="form-control" wire:model="statForm.clean_sheets" min="0">
                                @error('statForm.clean_sheets') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">النقاط</label>
                                <input type="number" class="form-control" wire:model="statForm.points" min="0">
                                @error('statForm.points') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12"><hr class="my-2"><small class="text-muted fw-bold">الإحصائيات التفصيلية</small></div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">البطاقات الصفراء</label>
                                <input type="number" class="form-control" wire:model="statForm.yellow_cards" min="0">
                                @error('statForm.yellow_cards') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">البطاقات الحمراء</label>
                                <input type="number" class="form-control" wire:model="statForm.red_cards" min="0">
                                @error('statForm.red_cards') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">متوسط الاستحواذ (%)</label>
                                <input type="number" class="form-control" wire:model="statForm.possession_avg" min="0" max="100" step="0.1">
                                @error('statForm.possession_avg') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">تسديدات لكل مباراة</label>
                                <input type="number" class="form-control" wire:model="statForm.shots_per_match" min="0" step="0.1">
                                @error('statForm.shots_per_match') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">إلغاء</button>
                        <button type="button" class="btn btn-warning px-4" wire:click="saveStat" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveStat">
                                <i class="bi bi-check-lg"></i> {{ $editingStatId ? 'تحديث' : 'حفظ' }}
                            </span>
                            <span wire:loading wire:target="saveStat">
                                <span class="spinner-border spinner-border-sm"></span> جاري الحفظ...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
