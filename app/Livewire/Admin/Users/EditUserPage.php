<?php
namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.admin')]
class EditUserPage extends Component
{
    public User $user;
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public ?string $password = null;
    public string $role = '';
    public string $is_verified = '';

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_verified = $user->is_verified ? '1' : '0';
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|unique:users,username,' . $this->user->id,
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'role' => 'required|in:viewer,competitor,captain,player,organizer,admin,user',
        ]);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'is_verified' => (bool) $this->is_verified,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $this->user->update($data);
        $this->user->syncRoles([$this->role]);

        session()->flash('success', 'تم تحديث المستخدم بنجاح');
        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.users.edit-user-page', [
            'title' => 'تعديل مستخدم',
            'user' => $this->user,
        ]);
    }
}
