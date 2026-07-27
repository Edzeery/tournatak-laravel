<div class="auth-wrapper">
    <div class="hero-shape" style="width:600px;height:600px;top:-300px;right:-200px;"></div>
    <div class="hero-shape" style="width:400px;height:400px;bottom:-200px;left:-150px;"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card">
                    <div class="text-center d-lg-none mb-4">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <span class="text-gold fw-bold" style="font-size:1.5rem;">
                                <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                            </span>
                        </a>
                    </div>

                    <div class="auth-logo">
                        <i class="bi bi-shield-lock-fill text-dark"></i>
                    </div>
                    <h2>إعادة تعيين كلمة المرور</h2>
                    <p class="auth-subtitle">أدخل كلمة المرور الجديدة لحسابك</p>

                    @if($errors->any())
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.2);color:#fca5a5;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <div style="font-size:0.85rem;">{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form wire:submit="resetPassword">
                        <input type="hidden" wire:model="token">

                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <div class="position-relative">
                                <i class="bi bi-envelope position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="email" class="form-control" style="padding-right:42px;" placeholder="example@email.com" wire:model="email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">كلمة المرور الجديدة</label>
                            <div class="position-relative">
                                <i class="bi bi-lock position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="password" class="form-control" style="padding-right:42px;" placeholder="••••••••" wire:model="password" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <div class="position-relative">
                                <i class="bi bi-lock-fill position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="password" class="form-control" style="padding-right:42px;" placeholder="••••••••" wire:model="password_confirmation" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold" style="font-size:1.05rem;" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="resetPassword">
                                <i class="bi bi-check-circle me-2"></i> إعادة تعيين كلمة المرور
                            </span>
                            <span wire:loading wire:target="resetPassword">
                                <span class="spinner-border spinner-border-sm me-2"></span> جاري الحفظ...
                            </span>
                        </button>
                    </form>

                    <div class="auth-divider">أو</div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="auth-link" style="font-size:0.95rem;">
                            <i class="bi bi-arrow-right me-1"></i> العودة لتسجيل الدخول
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
