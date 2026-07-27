<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">المراكز</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-geo-alt-fill text-gold"></i> إدارة المراكز</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">مراكز اللاعبين (كرة قدم وفوتسال)</p>
        </div>
        <button class="btn btn-warning" wire:click="openModal">
            <i class="bi bi-plus-lg"></i> إضافة مركز
        </button>
    </div>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">بحث</label>
                    <input type="text" class="form-control" placeholder="بحث بالاسم أو الاختصار..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">نوع الرياضة</label>
                    <select class="form-select" wire:model.live="filterSport">
                        <option value="">الكل</option>
                        <option value="football">كرة قدم</option>
                        <option value="futsal">فوتسال</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0" wire:loading.opacity>
        <div class="card-body">
            @if($positions->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="font-size:0.8rem;">#</th>
                                <th style="font-size:0.8rem;">الاسم</th>
                                <th style="font-size:0.8rem;">الاسم (EN)</th>
                                <th style="font-size:0.8rem;">الاختصار</th>
                                <th style="font-size:0.8rem;">الفئة</th>
                                <th style="font-size:0.8rem;">الرياضية</th>
                                <th style="font-size:0.8rem;">الترتيب</th>
                                <th style="font-size:0.8rem;">الحالة</th>
                                <th style="font-size:0.8rem;">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positions as $pos)
                                <tr wire:key="{{ $pos->id }}">
                                    <td style="font-size:0.85rem;">{{ $loop->iteration }}</td>
                                    <td class="fw-bold" style="font-size:0.9rem;">{{ $pos->name }}</td>
                                    <td style="font-size:0.85rem;color:#64748b;">{{ $pos->name_en ?? '—' }}</td>
                                    <td><span class="badge bg-dark rounded-pill" style="font-size:0.75rem;">{{ $pos->abbreviation ?? '—' }}</span></td>
                                    <td style="font-size:0.85rem;">
                                        @php
                                            $catLabels = ['goalkeeper' => 'حارس', 'defender' => 'مدافع', 'midfielder' => 'لاعب وسط', 'forward' => 'مهاجم', 'player' => 'لاعب'];
                                            $catColors = ['goalkeeper' => '#f59e0b', 'defender' => '#3b82f6', 'midfielder' => '#10b981', 'forward' => '#ef4444', 'player' => '#64748b'];
                                        @endphp
                                        <span style="font-size:0.75rem;padding:3px 8px;border-radius:6px;background:{{ $catColors[$pos->category] ?? '#64748b' }}20;color:{{ $catColors[$pos->category] ?? '#64748b' }};">
                                            {{ $catLabels[$pos->category] ?? $pos->category }}
                                        </span>
                                    </td>
                                    <td style="font-size:0.85rem;">{{ $pos->sport_type === 'football' ? 'كرة قدم' : 'فوتسال' }}</td>
                                    <td style="font-size:0.85rem;">{{ $pos->sort_order }}</td>
                                    <td>
                                        @if($pos->is_active)
                                            <span class="badge" style="background:rgba(16,185,129,0.1);color:#10b981;font-size:0.75rem;">نشط</span>
                                        @else
                                            <span class="badge bg-secondary" style="font-size:0.75rem;">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-outline-primary" style="border-radius:8px;" wire:click="editPosition({{ $pos->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                                    wire:click="deletePosition({{ $pos->id }})"
                                                    wire:confirm="هل أنت متأكد من حذف هذا المركز؟">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-geo-alt d-block" style="font-size:2.5rem;"></i>
                        <h5>لا توجد مراكز</h5>
                        <p class="text-muted">لم يتم إضافة أي مراكز بعد</p>
                        <button class="btn btn-warning" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> إضافة مركز
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);" wire:click.self="closeModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:16px;" wire:click.stop>
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-geo-alt-fill text-gold"></i>
                            {{ $editingPositionId ? 'تعديل المركز' : 'إضافة مركز جديد' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">الاسم (عربي)</label>
                            <input type="text" class="form-control" placeholder="مثل: حارس المرمى" wire:model="positionForm.name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">الاسم (إنجليزي)</label>
                            <input type="text" class="form-control" placeholder="e.g. Goalkeeper" wire:model="positionForm.name_en">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">الاختصار</label>
                                <input type="text" class="form-control" placeholder="مثل: GK" wire:model="positionForm.abbreviation" maxlength="10">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">الترتيب</label>
                                <input type="number" class="form-control" wire:model="positionForm.sort_order" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">الفئة</label>
                                <select class="form-select" wire:model="positionForm.category">
                                    <option value="goalkeeper">حارس مرمى</option>
                                    <option value="defender">مدافع</option>
                                    <option value="midfielder">لاعب وسط</option>
                                    <option value="forward">مهاجم</option>
                                    <option value="player">لاعب</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">نوع الرياضة</label>
                                <select class="form-select" wire:model="positionForm.sport_type">
                                    <option value="football">كرة قدم</option>
                                    <option value="futsal">فوتسال</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model="positionForm.is_active" id="posActive">
                                <label class="form-check-label fw-bold" for="posActive">نشط</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" style="border-radius:8px;">إلغاء</button>
                        <button type="button" class="btn btn-warning px-4" wire:click="savePosition" wire:loading.attr="disabled" style="border-radius:8px;">
                            <span wire:loading.remove wire:target="savePosition"><i class="bi bi-check-lg"></i> حفظ</span>
                            <span wire:loading wire:target="savePosition"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
