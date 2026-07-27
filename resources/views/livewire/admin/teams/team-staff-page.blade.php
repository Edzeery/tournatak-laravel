<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="text-decoration-none" style="color:var(--primary);">الفرق</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.edit', $team) }}" class="text-decoration-none" style="color:var(--primary);">{{ $team->name }}</a></li>
            <li class="breadcrumb-item active">الطاقم</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-people-fill text-gold"></i> الطاقم الفني</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">{{ $team->name }}</p>
        </div>
        <button class="btn btn-warning" wire:click="openModal">
            <i class="bi bi-plus-lg"></i> إضافة عضو طاقم
        </button>
    </div>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">بحث</label>
                    <input type="text" class="form-control" placeholder="بحث باسم العضو..." wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3" wire:loading.opacity>
        @forelse($staff as $member)
            <div class="col-md-4 col-lg-3" wire:key="{{ $member->id }}">
                <div class="card border-0 h-100" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <div class="card-body text-center p-3">
                        <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle bg-light" style="width:56px;height:56px;">
                            <i class="bi {{ $staffIcons[$member->staff_role] ?? 'bi-person' }} text-gold" style="font-size:1.5rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-1" style="font-size:0.9rem;">{{ $member->user->name ?? '—' }}</h6>
                        <span class="badge bg-warning-subtle text-warning fw-bold mb-2" style="font-size:0.75rem;">
                            {{ $staffRoles[$member->staff_role] ?? $member->staff_role }}
                        </span>
                        @if($member->specialization)
                            <p class="text-muted mb-2" style="font-size:0.8rem;">
                                <i class="bi bi-tag"></i> {{ $member->specialization }}
                            </p>
                        @endif
                        <div class="text-muted mb-2" style="font-size:0.75rem;">
                            @if($member->start_date)
                                <div><i class="bi bi-calendar-event"></i> {{ $member->start_date->format('Y/m/d') }}</div>
                            @endif
                            @if($member->end_date)
                                <div><i class="bi bi-calendar-check"></i> {{ $member->end_date->format('Y/m/d') }}</div>
                            @endif
                        </div>
                        <div class="d-flex gap-1 justify-content-center mt-auto pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary" style="border-radius:8px;" wire:click="editStaff({{ $member->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                    wire:click="deleteStaff({{ $member->id }})"
                                    wire:confirm="هل أنت متأكد من حذف هذا العضو؟">
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
                            <i class="bi bi-people d-block" style="font-size:2.5rem;"></i>
                            <h5>لا يوجد أعضاء طاقم</h5>
                            <p class="text-muted">لم يتم إضافة أي عضو طاقم بعد</p>
                            <button class="btn btn-warning" wire:click="openModal">
                                <i class="bi bi-plus-lg"></i> إضافة عضو طاقم
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);" wire:click.self="closeModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:16px;" wire:click.stop>
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-people-fill text-gold"></i>
                            {{ $editingStaffId ? 'تعديل عضو الطاقم' : 'إضافة عضو طاقم' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">بحث عن مستخدم</label>
                            <input type="text" class="form-control" placeholder="اكتب اسم المستخدم للبحث..."
                                   wire:model.live.debounce.300ms="userSearch">
                            @if(count($searchedUsers) > 0)
                                <div class="list-group mt-1" style="max-height:200px;overflow-y:auto;position:relative;z-index:10;">
                                    @foreach($searchedUsers as $u)
                                        <button type="button" class="list-group-item list-group-item-action"
                                                wire:click="selectUser({{ $u->id }})">
                                            {{ $u->name }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">الدور</label>
                            <select class="form-select" wire:model="staffForm.staff_role">
                                <option value="">اختر الدور...</option>
                                @foreach($staffRoles as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">التخصص</label>
                            <input type="text" class="form-control" placeholder="التخصص..." wire:model="staffForm.specialization">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">تاريخ البداية</label>
                                <input type="text" class="form-control flatpickr-input" wire:model="staffForm.start_date" placeholder="{{ __('app.select_date') }}" data-date-format="Y-m-d" data-alt-format="d/m/Y">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">تاريخ النهاية</label>
                                <input type="text" class="form-control flatpickr-input" wire:model="staffForm.end_date" placeholder="{{ __('app.select_date') }}" data-date-format="Y-m-d" data-alt-format="d/m/Y">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" style="border-radius:8px;">إلغاء</button>
                        <button type="button" class="btn btn-warning px-4" wire:click="saveStaff" wire:loading.attr="disabled" style="border-radius:8px;">
                            <span wire:loading.remove wire:target="saveStaff"><i class="bi bi-check-lg"></i> حفظ</span>
                            <span wire:loading wire:target="saveStaff"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
