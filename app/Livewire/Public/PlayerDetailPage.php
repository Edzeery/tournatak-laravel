<?php

namespace App\Livewire\Public;

use App\Models\Player;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PlayerDetailPage extends Component
{
    public $playerId;
    public $player;

    public function mount(int $playerId): void
    {
        $this->playerId = $playerId;
        $this->player = Player::with([
            'user',
            'team',
            'position',
            'goals.match',
        ])->findOrFail($playerId);
    }

    public function render()
    {
        $goals = $this->player->goals()->with('match')->latest()->get();
        $totalGoals = $goals->count();

        return view('livewire.public.player-detail-page', [
            'title' => $this->player->name ?? 'لاعب',
            'player' => $this->player,
            'goals' => $goals,
            'totalGoals' => $totalGoals,
            'seasonStats' => $this->player->seasonStats()->with('competition')->get(),
        ]);
    }
}
