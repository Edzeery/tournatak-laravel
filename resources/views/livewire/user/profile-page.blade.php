<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-person-gear text-gold"></i> الملف الشخصي
            </h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">تعديل معلوماتك الشخصية</p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary" style="border-radius:10px;">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        {{-- Profile Form --}}
        <div class="col-lg-8">
            <div class="card border-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square text-gold"></i> تعديل المعلومات</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">الاسم الكامل</label>
                                <input type="text" class="form-control" wire:model="full_name" required
                                    placeholder="الاسم الكامل">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">تاريخ الميلاد</label>
                                <input type="text" class="form-control flatpickr-input"
                                    wire:model="profile_date_birth" placeholder="{{ __('app.select_date') }}"
                                    data-date-format="Y-m-d" data-alt-format="d/m/Y">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save"><i class="bi bi-check-lg"></i> حفظ
                                التعديلات</span>
                            <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm"></span>
                                جاري الحفظ...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Account Info --}}
        <div class="col-lg-4">
            <div class="card border-0">
                <div class="card-body p-4 text-center">
                    <div class="bg-gold text-dark rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3"
                        style="width:80px;height:80px;font-size:2rem;">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p style="color:#94a3b8;font-size:0.9rem;">{{ $user->email }}</p>
                    <div class="mb-3">
                        <x-status-badge domain="role" status="{{ $user->role }}" set="bi" />
                    </div>
                    <hr>
                    <div class="text-start">
                        <div class="d-flex justify-content-between mb-2" style="font-size:0.9rem;">

                                <small style="color:#94a3b8;"> {{ __('attributes.username') }} </small>
                                <small class=" fw-bold ">{{ $user->username }}</small>

                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size:0.9rem;">
                            <span style="color:#94a3b8;">تاريخ التسجيل</span>
                            <span class="fw-bold">{{ formatDate($user->created_at) }}</span>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:0.9rem;">
                            <span style="color:#94a3b8;">التحقق</span>
                            @if ($user->is_verified)
                                <span class="fw-bold" style="color:#16a34a;">موثق</span>
                            @else
                                <span class="fw-bold" style="color:#ef4444;">غير موثق</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
