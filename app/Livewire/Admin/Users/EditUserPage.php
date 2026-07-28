<?php
namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Services\UserService;
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
        $this->authorize('update', $user);

        $this->user = $user;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_verified = $user->is_verified ? '1' : '0';
    }

    public function update()
    {
        $service = app(UserService::class);
        $this->validate($service->getUpdateValidationRules($this->user));

        $service->update($this->user, [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'is_verified' => (bool) $this->is_verified,
        ]);

        session()->flash('success', __('app.user_updated'));
        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.users.edit-user-page', [
            'title' => __('app.page_title_edit_user'),
            'user' => $this->user,
        ]);
    }
}
