<div class="container py-4" style="max-width:900px;">
    <div class="mb-4">
        <h2 class="fw-bold" style="color:#fff;">
            <i class="bi bi-shield-fill-check text-gold me-2"></i> الأمان
        </h2>
        <p style="color:rgba(255,255,255,0.5);font-size:0.9rem;">إدارة إعدادات أمان حسابك</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="auth-card text-center p-4">
                <div style="width:64px;height:64px;border-radius:16px;background:rgba(255,193,7,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="bi bi-shield-lock" style="font-size:1.8rem;color:#ffc107;"></i>
                </div>
                <h5 class="fw-bold" style="color:#fff;">المصادقة الثنائية</h5>
                <p style="color:rgba(255,255,255,0.5);font-size:0.85rem;">أضف طبقة حماية إضافية عبر تطبيق المصادقة</p>
                <a href="{{ route('user.2fa-setup') }}" class="btn btn-sm mt-2" style="background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.2);color:#ffc107;">
                    <i class="bi bi-gear me-1"></i> الإعداد
                </a>
            </div>
        </div>
    </div>
</div>
