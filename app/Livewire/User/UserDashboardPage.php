<?php

namespace App\Livewire\User;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserDashboardPage extends Component
{
    public function render()
    {
        return view('livewire.user.user-dashboard-page', [
            'title' => 'لوحة التحكم',
            'user' => auth()->user(),
        ]);
    }
}
