<?php

namespace App\Livewire\Public;

use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TeamDetailPage extends Component
{
    public $teamId;

    public $team;

    public function mount(int $teamId): void
    {
        $this->teamId = $teamId;
        $this->team = Team::with([
            'captain',
            'players.user',
            'players.position',
            'activeStaff.user',
            'formations',
        ])->findOrFail($teamId);
    }

    public function render()
    {
        return view('livewire.public.team-detail-page', [
            'title' => $this->team->name,
            'team' => $this->team,
            'players' => $this->team->players()->with('user')->orderBy('number')->get(),
            'staff' => $this->team->activeStaff()->with('user')->get(),
            'formations' => $this->team->formations()->latest()->get(),
        ]);
    }
}
