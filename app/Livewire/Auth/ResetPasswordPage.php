<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;

#[Layout('layouts.app')]
class ResetPasswordPage extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker()->reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.');
            return redirect()->route('login');
        }

        $this->addError('email', 'رابط إعادة التعيين غير صالح أو انتهت صلاحيته.');
    }

    public function render()
    {
        return view('livewire.auth.reset-password-page', [
            'title' => 'إعادة تعيين كلمة المرور',
        ]);
    }
}
