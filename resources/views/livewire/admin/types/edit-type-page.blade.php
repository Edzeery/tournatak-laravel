<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.types.index') }}" class="text-decoration-none" style="color:var(--primary);">الأنواع</a></li>
            <li class="breadcrumb-item active">تعديل النوع</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-pencil text-gold"></i> تعديل النوع</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">{{ $type->name }}</p>
        </div>
        <a href="{{ route('admin.types.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>

    <div class="card border-0">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form wire:submit="update">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">اسم النوع</label>
                        <input type="text" class="form-control" wire:model="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الرابط (Slug)</label>
                        <input type="text" class="form-control" wire:model="slug" readonly style="background:#f8f9fa;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">التصنيف الفرعي</label>
                        <select class="form-select" wire:model="subtype_id" required>
                            @foreach($subtypes as $subtype)
                                <option value="{{ $subtype->id }}" {{ $subtype->id == $type->subtype_id ? 'selected' : '' }}>{{ $subtype->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الأيقونة</label>
                        <input type="text" class="form-control" wire:model="icon">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الترتيب</label>
                        <input type="number" class="form-control" wire:model="sort_order" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الحالة</label>
                        <select class="form-select" wire:model="is_active">
                            <option value="1">نشط</option>
                            <option value="0">غير نشط</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">الوصف</label>
                        <textarea class="form-control" wire:model="description" rows="2"></textarea>
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
