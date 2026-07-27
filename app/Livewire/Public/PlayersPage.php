<?php

namespace App\Livewire\Public;

use App\Models\Player;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PlayersPage extends Component
{
    public function render()
    {
        return view('livewire.public.players-page', [
            'title' => 'اللاعبون',
            'players' => Player::with(['user', 'team'])
                ->withCount('goals')
                ->latest()
                ->paginate(12),
        ]);
    }
}
