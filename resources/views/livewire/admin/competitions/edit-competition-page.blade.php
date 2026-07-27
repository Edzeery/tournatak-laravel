<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.competitions.index') }}" class="text-decoration-none" style="color:var(--primary);">البطولات</a></li>
            <li class="breadcrumb-item active">تعديل البطولة</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-pencil text-gold"></i> تعديل البطولة</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">{{ $competition->name }}</p>
        </div>
        <a href="{{ route('admin.competitions.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>

    <div class="card border-0">
        <div class="card-body p-4">
            <form wire:submit="update">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">اسم البطولة</label>
                        <input type="text" class="form-control" wire:model="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">نوع البطولة</label>
                        <select class="form-select" wire:model="type_id" required>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ $type->id == $competition->type_id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">النوع الفرعي</label>
                        <select class="form-select" wire:model="subtype_id" required>
                            @foreach($subtypes as $subtype)
                                <option value="{{ $subtype->id }}" {{ $subtype->id == $competition->subtype_id ? 'selected' : '' }}>{{ $subtype->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الحالة</label>
                        <select class="form-select" wire:model="status">
                            <option value="draft">مسودة</option>
                            <option value="upcoming">قريباً</option>
                            <option value="ongoing">جارية</option>
                            <option value="completed">مكتملة</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الموافقة</label>
                        <select class="form-select" wire:model="approval_status">
                            <option value="pending">قيد المراجعة</option>
                            <option value="approved">موثقة</option>
                            <option value="rejected">مرفوضة</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الموقع</label>
                        <input type="text" class="form-control" wire:model="location">
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
                        <textarea class="form-control" wire:model="description" rows="3"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="update"><i class="bi bi-check-lg"></i> حفظ التعديلات</span>
                    <span wire:loading wire:target="update"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                </button>
            </form>
        </div>
    </div>
</div>
