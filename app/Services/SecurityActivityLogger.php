<?php

namespace App\Services;

use App\Models\Activity;

class SecurityActivityLogger
{
    public static function log(
        string $event,
        ?int $userId = null,
        ?string $description = null,
        array $properties = [],
    ): void {
        $request = request();

        Activity::create([
            'user_id' => $userId ?? auth()->id(),
            'type' => 'security',
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    public static function login($user): void
    {
        static::log('login', $user->id, 'تسجيل دخول ناجح');
    }

    public static function failedLogin(string $identifier): void
    {
        static::log('failed_login', null, 'محاولة تسجيل دخول فاشلة', [
            'identifier' => $identifier,
        ]);
    }

    public static function logout($user): void
    {
        static::log('logout', $user->id, 'تسجيل خروج');
    }

    public static function passwordChanged($user): void
    {
        static::log('password_changed', $user->id, 'تم تغيير كلمة المرور');
    }

    public static function passwordResetRequested($email): void
    {
        static::log('password_reset_requested', null, 'طلب إعادة تعيين كلمة المرور', [
            'email' => $email,
        ]);
    }

    public static function twoFactorEnabled($user): void
    {
        static::log('2fa_enabled', $user->id, 'تم تفعيل المصادقة الثنائية');
    }

    public static function twoFactorDisabled($user): void
    {
        static::log('2fa_disabled', $user->id, 'تم تعطيل المصادقة الثنائية');
    }

    public static function twoFactorChallengePassed($user): void
    {
        static::log('2fa_challenge_passed', $user->id, 'تم تجاوز تحدي المصادقة الثنائية');
    }

    public static function recoveryCodeUsed($user): void
    {
        static::log('recovery_code_used', $user->id, 'تم استخدام رمز استرداد');
    }

    public static function emailVerified($user): void
    {
        static::log('email_verified', $user->id, 'تم التحقق من البريد الإلكتروني');
    }

    public static function accountCreated($user): void
    {
        static::log('account_created', $user->id, 'تم إنشاء الحساب');
    }
}
