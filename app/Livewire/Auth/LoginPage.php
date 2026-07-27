<?php
namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class LoginPage extends Component
{
    public string $identifier = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $credentials = filter_var($this->identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $this->identifier, 'password' => $this->password]
            : ['username' => $this->identifier, 'password' => $this->password];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            $user = Auth::user();
            if (!$user->is_verified) {
                return redirect()->route('home')->with('error', 'يرجى تفعيل حسابك أولاً');
            }
            return redirect()->intended(route('home'))->with('success', 'مرحباً بك ' . $user->name);
        }

        return back()->withInput()->withErrors(['identifier' => 'بيانات تسجيل الدخول غير صحيحة']);
    }

    public function render()
    {
        return view('livewire.auth.login-page', [
            'title' => 'تسجيل الدخول',
        ]);
    }
}
