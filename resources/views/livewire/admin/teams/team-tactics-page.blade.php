<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="text-decoration-none" style="color:var(--primary);">الفرق</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.edit', $team) }}" class="text-decoration-none" style="color:var(--primary);">{{ $team->name }}</a></li>
            <li class="breadcrumb-item active">التكتيكات</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-lightning-charge-fill text-gold"></i> التكتيكات</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">{{ $team->name }}</p>
        </div>
        <button class="btn btn-warning" wire:click="openModal">
            <i class="bi bi-plus-lg"></i> إضافة تكتيك
        </button>
    </div>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">بحث</label>
                    <input type="text" class="form-control" placeholder="بحث بالاسم أو التشكيلة..." wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3" wire:loading.opacity>
        @forelse($tactics as $tactic)
            <div class="col-md-6 col-lg-4" wire:key="{{ $tactic->id }}">
                <div class="card border-0 h-100" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0">{{ $tactic->name }}</h6>
                            @if($tactic->is_default)
                                <span class="badge bg-warning text-dark" style="font-size:0.7rem;">
                                    <i class="bi bi-star-fill"></i> افتراضي
                                </span>
                            @endif
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-danger-subtle text-danger fw-bold me-1 mb-1" style="font-size:0.7rem;">
                                <i class="bi bi-speedometer"></i> {{ $pressingStyles[$tactic->pressing_style] ?? $tactic->pressing_style }}
                            </span>
                            <span class="badge bg-primary-subtle text-primary fw-bold me-1 mb-1" style="font-size:0.7rem;">
                                <i class="bi bi-arrows-angle-expand"></i> {{ $buildUpStyles[$tactic->build_up_style] ?? $tactic->build_up_style }}
                            </span>
                            <span class="badge bg-success-subtle text-success fw-bold me-1 mb-1" style="font-size:0.7rem;">
                                <i class="bi bi-shield-fill"></i> {{ $defenseStyles[$tactic->defense_style] ?? $tactic->defense_style }}
                            </span>
                            <span class="badge bg-warning-subtle text-warning fw-bold me-1 mb-1" style="font-size:0.7rem;">
                                <i class="bi bi-lightning"></i> {{ $attackStyles[$tactic->attack_style] ?? $tactic->attack_style }}
                            </span>
                        </div>

                        @if($tactic->formation_used)
                            <div class="mb-2">
                                <span class="badge bg-info-subtle text-info fw-bold" style="font-size:0.75rem;">
                                    <i class="bi bi-diagram-3"></i> {{ $tactic->formation_used }}
                                </span>
                            </div>
                        @endif

                        @if($tactic->notes)
                            <p class="text-muted mb-2" style="font-size:0.8rem;">{{ Str::limit($tactic->notes, 100) }}</p>
                        @endif

                        <div class="d-flex gap-1 justify-content-end pt-2 border-top mt-auto">
                            <button class="btn btn-sm btn-outline-primary" style="border-radius:8px;" wire:click="editTactic({{ $tactic->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                    wire:click="deleteTactic({{ $tactic->id }})"
                                    wire:confirm="هل أنت متأكد من حذف هذا التكتيك؟">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-body py-5 text-center">
                        <div class="empty-state py-3">
                            <i class="bi bi-lightning-charge d-block" style="font-size:2.5rem;"></i>
                            <h5>لا توجد تكتيكات</h5>
                            <p class="text-muted">لم يتم إضافة أي تكتيك بعد</p>
                            <button class="btn btn-warning" wire:click="openModal">
                                <i class="bi bi-plus-lg"></i> إضافة تكتيك
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);" wire:click.self="closeModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="border-radius:16px;" wire:click.stop>
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-lightning-charge-fill text-gold"></i>
                            {{ $editingTacticId ? 'تعديل التكتيك' : 'إضافة تكتيك' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم التكتيك</label>
                            <input type="text" class="form-control" placeholder="مثال: التكتيك الأساسي" wire:model="tacticForm.name">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">أسلوب الضغط</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($pressingStyles as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="pressing_style" id="pressing_{{ $key }}" value="{{ $key }}" wire:model="tacticForm.pressing_style">
                                            <label class="form-check-label fw-bold" for="pressing_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">أسلوب البناء</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($buildUpStyles as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="build_up_style" id="buildup_{{ $key }}" value="{{ $key }}" wire:model="tacticForm.build_up_style">
                                            <label class="form-check-label fw-bold" for="buildup_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">أسلوب الدفاع</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($defenseStyles as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="defense_style" id="defense_{{ $key }}" value="{{ $key }}" wire:model="tacticForm.defense_style">
                                            <label class="form-check-label fw-bold" for="defense_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">أسلوب الهجوم</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($attackStyles as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="attack_style" id="attack_{{ $key }}" value="{{ $key }}" wire:model="tacticForm.attack_style">
                                            <label class="form-check-label fw-bold" for="attack_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">التشكيلة المستخدمة</label>
                                <select class="form-select" wire:model="tacticForm.formation_used">
                                    <option value="">بدون تشكيلة</option>
                                    @foreach($formationOptions as $fo)
                                        <option value="{{ $fo }}">{{ $fo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">التكتيك الافتراضي</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="tacticForm.is_default" id="isDefaultTactic">
                                    <label class="form-check-label fw-bold" for="isDefaultTactic">تكتيك افتراضي</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ملاحظات</label>
                            <textarea class="form-control" rows="3" placeholder="ملاحظات إضافية حول التكتيك..." wire:model="tacticForm.notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" style="border-radius:8px;">إلغاء</button>
                        <button type="button" class="btn btn-warning px-4" wire:click="saveTactic" wire:loading.attr="disabled" style="border-radius:8px;">
                            <span wire:loading.remove wire:target="saveTactic"><i class="bi bi-check-lg"></i> حفظ</span>
                            <span wire:loading wire:target="saveTactic"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
