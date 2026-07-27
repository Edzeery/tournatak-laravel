<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="text-decoration-none" style="color:var(--primary);">الفرق</a></li>
            <li class="breadcrumb-item active">إضافة جديدة</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-plus-circle text-gold"></i> إضافة فريق جديد</h4>
        </div>
        <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
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

            <form wire:submit="store">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">اسم الفريق</label>
                        <input type="text" class="form-control" wire:model="name" required placeholder="اسم الفريق">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">القائد</label>
                        <select class="form-select" wire:model="captain_id">
                            <option value="">اختر القائد...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">رابط الشعار</label>
                        <input type="text" class="form-control" wire:model="logo" placeholder="https://...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">النقاط</label>
                        <input type="number" class="form-control" wire:model="points" min="0" value="0">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> حفظ الفريق</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                </button>
            </form>
        </div>
    </div>
</div>
