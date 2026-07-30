<?php

namespace App\Livewire\Admin\Users;

use App\Services\UserService;
use Livewire\Attributes\Layout;
use Livewire\Component;

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
        $service = app(UserService::class);
        $this->validate($service->getCreateValidationRules());

        $service->create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'is_verified' => (bool) $this->is_verified,
        ]);

        session()->flash('success', __('app.user_created'));

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.users.create-user-page', [
            'title' => __('app.page_title_add_user'),
        ]);
    }
}
