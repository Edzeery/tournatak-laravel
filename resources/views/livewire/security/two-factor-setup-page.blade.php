<div class="container py-4" style="max-width:700px;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color:#fff;">
                <i class="bi bi-shield-lock-fill text-gold me-2"></i> المصادقة الثنائية
            </h2>
            <p style="color:rgba(255,255,255,0.5);font-size:0.9rem;">أضف طبقة حماية إضافية لحسابك</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="background:rgba(22,163,74,0.15);border:1px solid rgba(22,163,74,0.2);color:#86efac;">
            <i class="bi bi-check-circle-fill"></i>
            <div style="font-size:0.9rem;">{{ session('success') }}</div>
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

    {{-- Status Card --}}
    <div class="auth-card mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:{{ $isEnabled ? 'rgba(22,163,74,0.15)' : 'rgba(239,68,68,0.15)' }};display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-{{ $isEnabled ? 'shield-fill-check' : 'shield-x' }}" style="font-size:1.4rem;color:{{ $isEnabled ? '#22c55e' : '#ef4444' }};"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" style="color:#fff;">{{ $isEnabled ? 'المصادقة الثنائية مفعّلة' : 'المصادقة الثنائية غير مفعّلة' }}</h5>
                    <p style="color:rgba(255,255,255,0.5);font-size:0.85rem;margin:0;">
                        {{ $isEnabled ? 'حسابك محمي بإضافة طبقة حماية' : 'أضف حماية إضافية لحسابك عبر تطبيق المصادقة' }}
                    </p>
                </div>
            </div>
            <span class="badge" style="background:{{ $isEnabled ? 'rgba(22,163,74,0.2);color:#86efac' : 'rgba(239,68,68,0.2);color:#fca5a5' }};padding:8px 16px;border-radius:8px;font-size:0.85rem;">
                {{ $isEnabled ? 'مفعّل' : 'غير مفعّل' }}
            </span>
        </div>
    </div>

    {{-- Enable 2FA --}}
    @if(!$isEnabled && !$showSetupForm)
        <div class="auth-card">
            <h5 class="fw-bold mb-3" style="color:#fff;">
                <i class="bi bi-qr-code me-2 text-gold"></i> تفعيل عبر تطبيق المصادقة
            </h5>
            <p style="color:rgba(255,255,255,0.5);font-size:0.9rem;line-height:1.7;">
                استخدم تطبيقاً مثل Google Authenticator أو Authy لتوليد رموز تحقق ثنائية.
            </p>

            <form wire:submit="initiateSetup">
                <div class="mb-3">
                    <label class="form-label">أدخل كلمة المرور للتأكيد</label>
                    <div class="position-relative">
                        <i class="bi bi-lock position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                        <input type="password" class="form-control" style="padding-right:42px;" placeholder="••••••••" wire:model="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="initiateSetup">
                        <i class="bi bi-qr-code me-2"></i> إعداد المصادقة الثنائية
                    </span>
                    <span wire:loading wire:target="initiateSetup">
                        <span class="spinner-border spinner-border-sm me-2"></span> جاري التجهيز...
                    </span>
                </button>
            </form>
        </div>
    @endif

    {{-- Setup Form with QR Code --}}
    @if($showSetupForm)
        <div class="auth-card mb-4">
            <h5 class="fw-bold mb-3" style="color:#fff;">
                <i class="bi bi-qr-code me-2 text-gold"></i> المسح الضوئي للرمز
            </h5>
            <p style="color:rgba(255,255,255,0.5);font-size:0.9rem;line-height:1.7;">
                افتح تطبيق المصادقة وامسح الرمز التالي:
            </p>

            <div class="text-center mb-4 p-4" style="background:#fff;border-radius:16px;display:inline-block;">
                {!! $qrCodeSvg !!}
            </div>

            <div class="mb-4">
                <label class="form-label" style="color:rgba(255,255,255,0.7);">أو أدخل المفتاح يدوياً:</label>
                <div class="position-relative">
                    <input type="text" class="form-control" style="text-align:center;letter-spacing:4px;font-family:monospace;font-size:1.1rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);" value="{{ $secretKey }}" readonly>
                </div>
            </div>

            <form wire:submit="confirmSetup">
                <div class="mb-3">
                    <label class="form-label">أدخل رمز التحقق من التطبيق</label>
                    <div class="position-relative">
                        <i class="bi bi-key position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                        <input type="text" class="form-control" style="padding-right:42px;letter-spacing:8px;text-align:center;font-size:1.4rem;" placeholder="000000" wire:model="verificationCode" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-sport w-100 py-3 fw-bold" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmSetup">
                        <i class="bi bi-check-circle me-2"></i> تأكيد التفعيل
                    </span>
                    <span wire:loading wire:target="confirmSetup">
                        <span class="spinner-border spinner-border-sm me-2"></span> جاري التحقق...
                    </span>
                </button>
            </form>
        </div>
    @endif

    {{-- Recovery Codes --}}
    @if($isEnabled && count($recoveryCodes) > 0)
        <div class="auth-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0" style="color:#fff;">
                    <i class="bi bi-key me-2 text-gold"></i> رموز الاسترداد
                </h5>
                <button class="btn btn-sm" style="background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.2);color:#ffc107;" wire:click="$toggle('showRecoveryCodes')">
                    <i class="bi bi-{{ $showRecoveryCodes ? 'eye-slash' : 'eye' }} me-1"></i>
                    {{ $showRecoveryCodes ? 'إخفاء' : 'إظهار' }}
                </button>
            </div>

            @if($showRecoveryCodes)
                <div class="p-3 mb-3" style="background:rgba(255,193,7,0.05);border:1px solid rgba(255,193,7,0.1);border-radius:12px;">
                    <p style="color:rgba(255,255,255,0.5);font-size:0.85rem;margin-bottom:12px;">
                        احتفظ بهذه الرموز في مكان آمن. يمكنك استخدام كل رمز مرة واحدة فقط في حال فقدان الوصول إلى تطبيق المصادقة.
                    </p>
                    <div class="row g-2">
                        @foreach($recoveryCodes as $code)
                            <div class="col-6 col-md-4">
                                <div class="text-center p-2" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:8px;font-family:monospace;font-size:0.9rem;color:#ffc107;">
                                    {{ $code }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p style="color:rgba(255,255,255,0.4);font-size:0.85rem;">{{ count($recoveryCodes) }} رموز استرداد متاحة.</p>
            @endif
        </div>
    @endif

    {{-- Disable 2FA / Regenerate Recovery Codes --}}
    @if($isEnabled)
        <div class="auth-card mb-4">
            <h5 class="fw-bold mb-3" style="color:#fff;">
                <i class="bi bi-arrow-repeat me-2 text-gold"></i> إنشاء رموز استرداد جديدة
            </h5>
            <form wire:submit="generateNewRecoveryCodes">
                <div class="mb-3">
                    <label class="form-label">أدخل كلمة المرور للتأكيد</label>
                    <div class="position-relative">
                        <i class="bi bi-lock position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                        <input type="password" class="form-control" style="padding-right:42px;" placeholder="••••••••" wire:model="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-sm w-100" style="background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.2);color:#ffc107;" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="generateNewRecoveryCodes">
                        <i class="bi bi-arrow-repeat me-2"></i> إنشاء رموز جديدة
                    </span>
                    <span wire:loading wire:target="generateNewRecoveryCodes">
                        <span class="spinner-border spinner-border-sm me-2"></span> جاري الإنشاء...
                    </span>
                </button>
            </form>
        </div>

        <div class="auth-card" style="border-color:rgba(239,68,68,0.2);">
            <h5 class="fw-bold mb-3" style="color:#ef4444;">
                <i class="bi bi-exclamation-triangle me-2"></i> تعطيل المصادقة الثنائية
            </h5>
            <p style="color:rgba(255,255,255,0.5);font-size:0.85rem;line-height:1.7;">
                سيتم إزالة الحماية الإضافية من حسابك. يُنصح بالاحتفاظ بها مفعّلة.
            </p>
            <form wire:submit="disable2FA">
                <div class="mb-3">
                    <label class="form-label">أدخل كلمة المرور للتأكيد</label>
                    <div class="position-relative">
                        <i class="bi bi-lock position-absolute" style="right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                        <input type="password" class="form-control" style="padding-right:42px;" placeholder="••••••••" wire:model="password" required>
                    </div>
                </div>
                <button type="submit" class="btn w-100" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#ef4444;" wire:loading.attr="disabled" onclick="event.preventDefault(); confirmSweetAlert('{{ route('user.security.2fa') }}', '{{ addslashes(__('app.confirm_delete_title')) }}', '{{ addslashes(__('app.confirm_delete_message')) }}', '{{ addslashes(__('app.confirm_delete_yes')) }}', '{{ addslashes(__('app.confirm_delete_cancel')) }}'); this.closest('form').submit();">
                    <span wire:loading.remove wire:target="disable2FA">
                        <i class="bi bi-shield-x me-2"></i> تعطيل المصادقة الثنائية
                    </span>
                    <span wire:loading wire:target="disable2FA">
                        <span class="spinner-border spinner-border-sm me-2"></span> جاري التعطيل...
                    </span>
                </button>
            </form>
        </div>
    @endif
</div>
