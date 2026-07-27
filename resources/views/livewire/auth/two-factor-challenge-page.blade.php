<div class="auth-wrapper">
    <div class="hero-shape" style="width:600px;height:600px;top:-300px;right:-200px;"></div>
    <div class="hero-shape" style="width:400px;height:400px;bottom:-200px;left:-150px;"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-8 col-12">
                <div class="auth-card">
                    <div class="auth-logo">
                        <i class="bi bi-shield-lock-fill text-dark"></i>
                    </div>
                    <h2>المصادقة الثنائية</h2>
                    <p class="auth-subtitle">أدخل الرمز المكوّن من 6 أرقام من تطبيق المصادقة الخاص بك</p>

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

                    <form wire:submit="verify">
                        <div class="mb-4">
                            <label class="form-label">رمز التحقق</label>
                            <div class="position-relative">
                                <i class="bi bi-key position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                                <input type="text" class="form-control" style="padding-right:42px;letter-spacing:8px;text-align:center;font-size:1.4rem;" placeholder="000000" wire:model="code" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autofocus required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold" style="font-size:1.05rem;" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="verify">
                                <i class="bi bi-check-circle me-2"></i> تحقق
                            </span>
                            <span wire:loading wire:target="verify">
                                <span class="spinner-border spinner-border-sm me-2"></span> جاري التحقق...
                            </span>
                        </button>
                    </form>

                    <div class="auth-divider">أو</div>

                    <div class="text-center">
                        <span style="color:rgba(255,255,255,0.5);font-size:0.9rem;">
                            استخدم رمز استرداد
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
