<div class="auth-wrapper">
    {{-- Background Shapes --}}
    <div class="hero-shape" style="width:600px;height:600px;top:-300px;right:-200px;"></div>
    <div class="hero-shape" style="width:400px;height:400px;bottom:-200px;left:-150px;"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center">
            {{-- Left side: branding --}}
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center px-5" style="position:relative;z-index:2;">
                <a href="{{ route('home') }}" class="text-decoration-none mb-4">
                    <span class="text-gold fw-bold" style="font-size:1.8rem;">
                        <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                    </span>
                </a>
                <h1 class="text-white fw-bold mb-3" style="font-size:2.5rem; line-height:1.3;">
                    مرحباً بعودتك!<br>
                    <span class="text-gold">تابع مسيرتك الرياضية</span>
                </h1>
                <p style="color:rgba(255,255,255,0.5); font-size:1.1rem; max-width:420px;">
                    سجّل دخولك للوصول إلى لوحة التحكم الخاصة بك، وإدارة بطولاتك وفرقك ولاعبيك.
                </p>
                <div class="d-flex gap-4 mt-4">
                    <div class="d-flex align-items-center gap-2" style="color:rgba(255,255,255,0.4);">
                        <i class="bi bi-check-circle-fill text-gold"></i>
                        <span style="font-size:0.9rem;">إدارة البطولات</span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="color:rgba(255,255,255,0.4);">
                        <i class="bi bi-check-circle-fill text-gold"></i>
                        <span style="font-size:0.9rem;">تتبع النتائج</span>
                    </div>
                </div>
            </div>

            {{-- Right side: login form --}}
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card">
                    {{-- Mobile logo --}}
                    <div class="text-center d-lg-none mb-4">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <span class="text-gold fw-bold" style="font-size:1.5rem;">
                                <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                            </span>
                        </a>
                    </div>

                    <div class="auth-logo">
                        <i class="bi bi-box-arrow-in-right text-dark"></i>
                    </div>
                    <h2>تسجيل الدخول</h2>
                    <p class="auth-subtitle">أدخل بياناتك للوصول إلى حسابك</p>

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

                    <form wire:submit="login">
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني أو اسم المستخدم</label>
                            <div class="position-relative">
                                <i class="bi bi-person position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="text" class="form-control" style="padding-right:42px;" placeholder="example@email.com" wire:model="identifier" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور</label>
                            <div class="position-relative">
                                <i class="bi bi-lock position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="password" class="form-control" style="padding-right:42px;" placeholder="••••••••" wire:model="password" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" wire:model="remember" id="remember">
                                <label class="form-check-label" for="remember" style="font-size:0.9rem;">تذكرني</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold" style="font-size:1.05rem;" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="login">
                                <i class="bi bi-box-arrow-in-right me-2"></i> تسجيل الدخول
                            </span>
                            <span wire:loading wire:target="login">
                                <span class="spinner-border spinner-border-sm me-2"></span> جاري التحقق...
                            </span>
                        </button>
                    </form>

                    <div class="auth-divider">أو</div>

                    <div class="text-center">
                        <span style="color:rgba(255,255,255,0.5);">ليس لديك حساب؟</span>
                        <a href="{{ route('register') }}" class="auth-link">أنشئ حساباً مجانياً</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
