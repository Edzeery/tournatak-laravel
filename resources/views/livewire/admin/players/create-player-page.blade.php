<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.players.index') }}" class="text-decoration-none" style="color:var(--primary);">اللاعبون</a></li>
            <li class="breadcrumb-item active">إضافة جديدة</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-person-plus text-gold"></i> إضافة لاعب جديد</h4>
        </div>
        <a href="{{ route('admin.players.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
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
                        <label class="form-label fw-bold">المستخدم</label>
                        <select class="form-select" wire:model="user_id" required>
                            <option value="">اختر المستخدم...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الفريق</label>
                        <select class="form-select" wire:model="team_id" required>
                            <option value="">اختر الفريق...</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">رقم اللاعب</label>
                        <input type="number" class="form-control" wire:model="number" min="0" placeholder="10">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">المركز</label>
                        <input type="text" class="form-control" wire:model="position_text" placeholder="مهاجم / مدافع / حارس">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">رابط الصورة</label>
                        <input type="text" class="form-control" wire:model="image" placeholder="https://...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">المركز (من النظام)</label>
                        <select class="form-select" wire:model="position_id">
                            <option value="">اختر المركز...</option>
                            @php
                                $grouped = $positions->groupBy('category');
                            @endphp
                            @foreach($grouped as $category => $catPositions)
                                <optgroup label="{{ $category }}">
                                    @foreach($catPositions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">تاريخ الميلاد</label>
                        <input type="text" class="form-control flatpickr-input" wire:model="date_of_birth" placeholder="{{ __('app.select_date') }}" data-date-format="Y-m-d" data-alt-format="d/m/Y">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الجنسية</label>
                        <input type="text" class="form-control" wire:model="nationality" placeholder="مثال: لبناني">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">الطول (سم)</label>
                        <input type="number" class="form-control" wire:model="height" min="0" placeholder="175">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">الوزن (كغ)</label>
                        <input type="number" class="form-control" wire:model="weight" min="0" placeholder="70">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">القدم</label>
                        <select class="form-select" wire:model="foot">
                            <option value="">اختر...</option>
                            <option value="right">يمين</option>
                            <option value="left">يسار</option>
                            <option value="both">كلا القدمين</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">نوع الرياضة</label>
                        <select class="form-select" wire:model="sport_type" required>
                            <option value="football">كرة قدم</option>
                            <option value="futsal">كرة قدم صالات</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">نبذة عن اللاعب</label>
                        <textarea class="form-control" wire:model="bio" rows="3" placeholder="معلومات إضافية عن اللاعب..."></textarea>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="is_captain" id="is_captain">
                            <label class="form-check-label fw-bold" for="is_captain">قائد الفريق</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> حفظ اللاعب</span>
                    <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                </button>
            </form>
        </div>
    </div>
</div>
