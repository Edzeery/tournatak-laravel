<?php

namespace App\Livewire\User;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SecurityPage extends Component
{
    public function render()
    {
        return view('livewire.user.security-page', [
            'title' => __('app.page_title_security'),
        ]);
    }
}
