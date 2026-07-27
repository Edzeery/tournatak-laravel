<?php
namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\SecurityActivityLogger;

#[Layout('layouts.app')]
class LoginPage extends Component
{
    public string $identifier = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        // Rate limit: 5 attempts per minute per IP
        $throttleKey = 'login:' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            session()->flash('error', 'لقد تجاوزت الحد المسموح. يرجى المحاولة بعد ' . $seconds . ' ثانية.');
            return;
        }

        $credentials = filter_var($this->identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $this->identifier, 'password' => $this->password]
            : ['username' => $this->identifier, 'password' => $this->password];

        if (Auth::attempt($credentials, $this->remember)) {
            $user = Auth::user();

            if (!$user->is_verified) {
                Auth::logout();
                return redirect()->route('home')->with('error', 'يرجى تفعيل حسابك أولاً');
            }

            SecurityActivityLogger::login($user);

            // Check if 2FA is enabled
            if ($user->securitySetting?->twofa_app) {
                Auth::logout();
                session()->put('2fa_user_id', $user->id);
                return redirect()->route('2fa.challenge');
            }

            session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))->with('success', 'مرحباً بك ' . $user->name);
        }

        SecurityActivityLogger::failedLogin($this->identifier);
        RateLimiter::hit($throttleKey, 60);
        return back()->withInput()->withErrors(['identifier' => 'بيانات تسجيل الدخول غير صحيحة']);
    }

    public function render()
    {
        return view('livewire.auth.login-page', [
            'title' => 'تسجيل الدخول',
        ]);
    }
}
