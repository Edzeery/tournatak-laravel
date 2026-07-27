<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.competitions.index') }}" class="text-decoration-none" style="color:var(--primary);">البطولات</a></li>
            <li class="breadcrumb-item active">إضافة جديدة</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-trophy-fill text-gold"></i> إضافة بطولة جديدة</h4>
        </div>
        <a href="{{ route('admin.competitions.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>

    <div class="card border-0">
        <div class="card-body p-4">
            <form wire:submit="store">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">اسم البطولة</label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="أدخل اسم البطولة">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">نوع البطولة</label>
                        <select class="form-select" wire:model="type_id" required>
                            <option value="">اختر النوع...</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">النوع الفرعي</label>
                        <select class="form-select" wire:model="subtype_id" required>
                            <option value="">اختر النوع الفرعي...</option>
                            @foreach($subtypes as $subtype)
                                <option value="{{ $subtype->id }}">{{ $subtype->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الموقع</label>
                        <input type="text" class="form-control" wire:model="location" placeholder="المدينة أو الملعب">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">تاريخ البداية</label>
                        <input type="datetime-local" class="form-control" wire:model="start_date">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">تاريخ النهاية</label>
                        <input type="datetime-local" class="form-control" wire:model="end_date">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">الوصف</label>
                        <textarea class="form-control" wire:model="description" rows="3" placeholder="وصف البطولة..."></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> حفظ البطولة</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                </button>
            </form>
        </div>
    </div>
</div>
