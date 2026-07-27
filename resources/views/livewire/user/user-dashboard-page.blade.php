<div>
    {{-- Welcome Section --}}
    <div class="card border-0 mb-4" style="background: var(--gradient-hero);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="text-white fw-bold mb-1">مرحباً، {{ $user->name }}!</h4>
                    <p style="color:rgba(255,255,255,0.6);margin:0;">إليك نظرة عامة على حسابك ونشاطك في المنصة</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('user.profile') }}" class="btn btn-primary-sport">
                        <i class="bi bi-person-gear me-1"></i> تعديل الملف
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-gold bg-opacity-10 text-gold"><i class="bi bi-person"></i></div>
                <div class="stat-number" style="font-size:1.5rem;">{{ $user->name }}</div>
                <div class="stat-label">{{ $user->email }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10" style="color:#16a34a;"><i class="bi bi-shield-fill"></i></div>
                <div class="stat-number">{{ $user->teams()->count() }}</div>
                <div class="stat-label">فريق</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;"><i class="bi bi-trophy-fill"></i></div>
                <div class="stat-number">{{ $user->competitions()->count() }}</div>
                <div class="stat-label">بطولة</div>
            </div>
        </div>
    </div>

    {{-- Profile Summary --}}
    <div class="card border-0">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-person-badge text-gold"></i> معلومات الحساب</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8f9fa;">
                        <div class="bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:48px;height:48px;">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <small style="color:#94a3b8;">@{{ $user->username }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background:#f8f9fa;">
                        <div style="color:#94a3b8;font-size:0.8rem;margin-bottom:4px;">البريد الإلكتروني</div>
                        <div class="fw-bold">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background:#f8f9fa;">
                        <div style="color:#94a3b8;font-size:0.8rem;margin-bottom:4px;">الدور</div>
                        <div><x-status-badge domain="role" status="{{ $user->role }}" set="bi" /></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background:#f8f9fa;">
                        <div style="color:#94a3b8;font-size:0.8rem;margin-bottom:4px;">تاريخ التسجيل</div>
                        <div class="fw-bold">{{ $user->created_at->format('Y/m/d') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
