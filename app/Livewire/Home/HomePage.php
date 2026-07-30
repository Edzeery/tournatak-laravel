<?php

namespace App\Livewire\Home;

use App\Models\Competition;
use App\Models\Player;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class HomePage extends Component
{
    public function render()
    {
        return view('livewire.home.home-page', [
            'title' => __('app.home'),
            'stats' => [
                'competitions' => Competition::count(),
                'teams' => Team::count(),
                'players' => Player::count(),
            ],
            'activeCompetitions' => Competition::where('approval_status', 'approved')
                ->where('status', 'ongoing')
                ->with('organizer')
                ->latest()
                ->limit(6)
                ->get(),
            'teams' => Team::latest()->limit(6)->get(),
        ]);
    }
}
