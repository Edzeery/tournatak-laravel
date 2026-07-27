<?php
namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Services\SecurityActivityLogger;

#[Layout('layouts.app')]
class RegisterPage extends Component
{
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'viewer';

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:viewer,competitor,captain,player,organizer,user',
        ]);

        $user = User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'is_verified' => false,
        ]);

        $user->assignRole($this->role);

        SecurityActivityLogger::accountCreated($user);

        // Send verification email
        $user->sendEmailVerificationNotification();

        session()->flash('success', 'تم إنشاء الحساب بنجاح. يرجى التحقق من بريدك الإلكتروني لتفعيل الحساب.');
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.register-page', [
            'title' => 'إنشاء حساب',
        ]);
    }
}
