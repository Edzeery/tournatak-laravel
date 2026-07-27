<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none" style="color:var(--primary);">المستخدمون</a></li>
            <li class="breadcrumb-item active">إضافة جديدة</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-person-plus text-gold"></i> إضافة مستخدم جديد</h4>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>

    <div class="card border-0">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit="store">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الاسم الكامل</label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="الاسم الكامل">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">اسم المستخدم</label>
                        <input type="text" class="form-control" wire:model="username" required placeholder="username">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">البريد الإلكتروني</label>
                        <input type="email" class="form-control" wire:model="email" required placeholder="email@example.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">كلمة المرور</label>
                        <input type="password" class="form-control" wire:model="password" required placeholder="••••••••">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الدور</label>
                        <select class="form-select" wire:model="role" required>
                            <option value="viewer">مشاهد</option>
                            <option value="competitor">مشارك</option>
                            <option value="captain">قائد</option>
                            <option value="player">لاعب</option>
                            <option value="organizer">منظم</option>
                            <option value="admin">مدير</option>
                            <option value="user">مستخدم</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">التحقق</label>
                        <select class="form-select" wire:model="is_verified">
                            <option value="1">موثق</option>
                            <option value="0">غير موثق</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> حفظ المستخدم</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                </button>
            </form>
        </div>
    </div>
</div>
