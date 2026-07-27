<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matches.index') }}" class="text-decoration-none" style="color:var(--primary);">المباريات</a></li>
            <li class="breadcrumb-item active">تعديل المباراة</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-pencil text-gold"></i> تعديل المباراة</h4>
        </div>
        <a href="{{ route('admin.matches.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>

    <nav class="nav nav-pills mb-3">
        <a class="nav-link active" href="{{ route('admin.matches.edit', $match->id) }}"><i class="bi bi-pencil"></i> البيانات الأساسية</a>
        <a class="nav-link" href="{{ route('admin.matches.lineup', $match->id) }}"><i class="bi bi-people-fill"></i> التشكيلة</a>
        <a class="nav-link" href="{{ route('admin.matches.events', $match->id) }}"><i class="bi bi-clock-history"></i> الأحداث</a>
        <a class="nav-link" href="{{ route('admin.matches.stats', $match->id) }}"><i class="bi bi-bar-chart-line"></i> الإحصائيات</a>
    </nav>

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
                        <label class="form-label fw-bold">البطولة</label>
                        <select class="form-select" wire:model="competition_id" required>
                            @foreach($competitions as $competition)
                                <option value="{{ $competition->id }}" {{ $competition->id == $match->competition_id ? 'selected' : '' }}>{{ $competition->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">تاريخ المباراة</label>
                        <input type="text" class="form-control flatpickr-input" wire:model="match_date" placeholder="{{ __('app.select_date_time') }}" data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الفريق الأول</label>
                        <select class="form-select" wire:model="team1_id" required>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ $team->id == $match->team1_id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الفريق الثاني</label>
                        <select class="form-select" wire:model="team2_id" required>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ $team->id == $match->team2_id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الحالة</label>
                        <select class="form-select" wire:model="status">
                            <option value="scheduled">مجدولة</option>
                            <option value="live">جارية</option>
                            <option value="finished">مكتملة</option>
                            <option value="cancelled">ملغاة</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">نتيجة الفريق الأول</label>
                        <input type="number" class="form-control" wire:model="score_team1" min="0" value="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">نتيجة الفريق الثاني</label>
                        <input type="number" class="form-control" wire:model="score_team2" min="0" value="0">
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
