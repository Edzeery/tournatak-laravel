<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

#[Layout('layouts.app')]
class ForgotPasswordPage extends Component
{
    public string $email = '';

    public function sendResetLink()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        // Rate limit: 3 attempts per minute per email
        $throttleKey = 'password.reset:' . strtolower($this->email);

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            session()->flash('error', 'لقد تجاوزت الحد المسموح. يرجى المحاولة بعد ' . $seconds . ' ثانية.');
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $user = User::where('email', $this->email)->first();

        if ($user) {
            $status = Password::broker()->sendResetLink(
                ['email' => $this->email]
            );

            if ($status === Password::RESET_LINK_SENT) {
                session()->flash('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.');
                return;
            }
        }

        // Always show success to prevent email enumeration
        session()->flash('success', 'إذا كان البريد الإلكتروني مسجلاً، ستتلقى رسالة تحتوي على رابط إعادة تعيين كلمة المرور.');
    }

    public function render()
    {
        return view('livewire.auth.forgot-password-page', [
            'title' => 'نسيت كلمة المرور',
        ]);
    }
}
