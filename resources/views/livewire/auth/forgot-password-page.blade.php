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
                        <i class="bi bi-key-fill text-dark"></i>
                    </div>
                    <h2>نسيت كلمة المرور</h2>
                    <p class="auth-subtitle">أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة التعيين</p>

                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="background:rgba(22,163,74,0.15);border:1px solid rgba(22,163,74,0.2);color:#86efac;">
                            <i class="bi bi-check-circle-fill"></i>
                            <div style="font-size:0.9rem;">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.2);color:#fca5a5;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div style="font-size:0.9rem;">{{ session('error') }}</div>
                        </div>
                    @endif

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

                    <form wire:submit="sendResetLink">
                        <div class="mb-4">
                            <label class="form-label">البريد الإلكتروني</label>
                            <div class="position-relative">
                                <i class="bi bi-envelope position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="email" class="form-control" style="padding-right:42px;" placeholder="example@email.com" wire:model="email" required autofocus>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold" style="font-size:1.05rem;" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="sendResetLink">
                                <i class="bi bi-send me-2"></i> إرسال رابط إعادة التعيين
                            </span>
                            <span wire:loading wire:target="sendResetLink">
                                <span class="spinner-border spinner-border-sm me-2"></span> جاري الإرسال...
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
