<?php

namespace App\Livewire\Public;

use App\Models\Match_;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MatchLivePage extends Component
{
    public Match_ $match;

    public function mount(Match_ $match): void
    {
        $this->match = $match->load([
            'competition',
            'team1',
            'team2',
            'events' => function ($q) {
                $q->with(['player', 'relatedPlayer', 'team'])
                    ->orderBy('minute')
                    ->orderBy('added_time');
            },
            'lineups' => function ($q) {
                $q->with(['player', 'position'])
                    ->orderBy('is_starter', 'desc')
                    ->orderBy('jersey_number');
            },
            'stats' => fn($q) => $q->with('team'),
        ]);
    }

    public function render()
    {
        $team1Events = $this->match->events->where('team_id', $this->match->team1_id);
        $team2Events = $this->match->events->where('team_id', $this->match->team2_id);

        $team1Lineup = $this->match->lineups->where('team_id', $this->match->team1_id);
        $team2Lineup = $this->match->lineups->where('team_id', $this->match->team2_id);

        $team1Stats = $this->match->stats->where('team_id', $this->match->team1_id)->first();
        $team2Stats = $this->match->stats->where('team_id', $this->match->team2_id)->first();

        return view('livewire.public.match-live-page', [
            'title' => $this->match->team1?->name . ' vs ' . $this->match->team2?->name,
            'team1Events' => $team1Events,
            'team2Events' => $team2Events,
            'team1Lineup' => $team1Lineup,
            'team2Lineup' => $team2Lineup,
            'team1Stats' => $team1Stats,
            'team2Stats' => $team2Stats,
        ]);
    }
}
