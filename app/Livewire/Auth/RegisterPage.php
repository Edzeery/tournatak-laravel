<?php
namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\AuthService;
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
        $service = app(AuthService::class);
        $this->validate($service->getRegisterValidationRules());

        $service->register([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ]);

        session()->flash('success', __('app.account_created'));
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.register-page', [
            'title' => __('app.page_title_register'),
        ]);
    }
}
