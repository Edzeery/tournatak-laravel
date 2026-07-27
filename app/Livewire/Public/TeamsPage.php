<?php

namespace App\Livewire\Public;

use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TeamsPage extends Component
{
    public function render()
    {
        return view('livewire.public.teams-page', [
            'title' => 'الفرق',
            'teams' => Team::with('captain')->latest()->paginate(12),
        ]);
    }
}
