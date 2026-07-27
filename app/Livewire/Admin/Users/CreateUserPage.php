<?php
namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.admin')]
class CreateUserPage extends Component
{
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'viewer';
    public string $is_verified = '1';

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:viewer,competitor,captain,player,organizer,admin,user',
        ]);

        $user = User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'is_verified' => (bool) $this->is_verified,
        ]);

        $user->assignRole($this->role);
        $user->profile()->create(['full_name' => $this->name]);
        $user->securitySetting()->create([]);

        session()->flash('success', 'تم إنشاء المستخدم بنجاح');
        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.users.create-user-page', [
            'title' => 'إضافة مستخدم',
        ]);
    }
}
