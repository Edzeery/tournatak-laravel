<div class="auth-wrapper">
    {{-- Background Shapes --}}
    <div class="hero-shape" style="width:600px;height:600px;top:-300px;left:-200px;"></div>
    <div class="hero-shape" style="width:400px;height:400px;bottom:-200px;right:-150px;"></div>

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
                    انضم إلى<br>
                    <span class="text-gold">مجتمع الرياضة</span>
                </h1>
                <p style="color:rgba(255,255,255,0.5); font-size:1.1rem; max-width:420px;">
                    أنشئ حسابك مجاناً وابدأ في إدارة بطولاتك أو تسجيل فريقك والمشاركة في المسابقات الرياضية.
                </p>
                <div class="d-flex flex-column gap-3 mt-4">
                    <div class="d-flex align-items-center gap-3" style="color:rgba(255,255,255,0.4);">
                        <div class="bg-gold bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;min-width:40px;">
                            <i class="bi bi-trophy text-gold"></i>
                        </div>
                        <span style="font-size:0.95rem;">إنشاء وإدارة البطولات</span>
                    </div>
                    <div class="d-flex align-items-center gap-3" style="color:rgba(255,255,255,0.4);">
                        <div class="bg-gold bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;min-width:40px;">
                            <i class="bi bi-shield-check text-gold"></i>
                        </div>
                        <span style="font-size:0.95rem;">تسجيل الفرق واللاعبين</span>
                    </div>
                    <div class="d-flex align-items-center gap-3" style="color:rgba(255,255,255,0.4);">
                        <div class="bg-gold bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;min-width:40px;">
                            <i class="bi bi-graph-up-arrow text-gold"></i>
                        </div>
                        <span style="font-size:0.95rem;">متابعة النتائج والإحصائيات</span>
                    </div>
                </div>
            </div>

            {{-- Right side: register form --}}
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card" style="max-width:480px;">
                    {{-- Mobile logo --}}
                    <div class="text-center d-lg-none mb-4">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <span class="text-gold fw-bold" style="font-size:1.5rem;">
                                <i class="bi bi-trophy-fill"></i> {{ config('app.name') }}
                            </span>
                        </a>
                    </div>

                    <div class="auth-logo">
                        <i class="bi bi-person-plus text-dark"></i>
                    </div>
                    <h2>إنشاء حساب جديد</h2>
                    <p class="auth-subtitle">أنشئ حسابك وابدأ رحلتك الرياضية</p>

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

                    <form wire:submit="register">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">اسم المستخدم</label>
                                <div class="position-relative">
                                    <i class="bi bi-at position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                    <input type="text" class="form-control" style="padding-right:36px;" placeholder="username" wire:model="username" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">الاسم الكامل</label>
                                <div class="position-relative">
                                    <i class="bi bi-person position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                    <input type="text" class="form-control" style="padding-right:36px;" placeholder="محمد أحمد" wire:model="name" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <div class="position-relative">
                                <i class="bi bi-envelope position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="email" class="form-control" style="padding-right:42px;" placeholder="example@email.com" wire:model="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نوع الحساب</label>
                            <div class="position-relative">
                                <i class="bi bi-person-badge position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);z-index:5;"></i>
                                <select class="form-select" style="padding-right:42px;" wire:model="role" required>
                                    <option value="viewer">مشاهد</option>
                                    <option value="competitor">مشارك في مسابقات</option>
                                    <option value="captain">قائد فريق</option>
                                    <option value="player">لاعب</option>
                                    <option value="organizer">منظم بطولات</option>
                                    <option value="user">مستخدم عادي</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">كلمة المرور</label>
                                <div class="position-relative">
                                    <i class="bi bi-lock position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                    <input type="password" class="form-control" style="padding-right:36px;" placeholder="••••••••" wire:model="password" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">تأكيد كلمة المرور</label>
                                <div class="position-relative">
                                    <i class="bi bi-lock-fill position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                    <input type="password" class="form-control" style="padding-right:36px;" placeholder="••••••••" wire:model="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold mt-4" style="font-size:1.05rem;" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="register">
                                <i class="bi bi-rocket-takeoff me-2"></i> إنشاء الحساب
                            </span>
                            <span wire:loading wire:target="register">
                                <span class="spinner-border spinner-border-sm me-2"></span> جاري الإنشاء...
                            </span>
                        </button>
                    </form>

                    <div class="auth-divider">أو</div>

                    <div class="text-center">
                        <span style="color:rgba(255,255,255,0.5);">لديك حساب بالفعل؟</span>
                        <a href="{{ route('login') }}" class="auth-link">تسجيل الدخول</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
