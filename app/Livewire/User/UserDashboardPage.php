<?php

namespace App\Livewire\User;

use App\Models\MatchEvent;
use App\Models\Match_;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserDashboardPage extends Component
{
    public function render()
    {
        $user = auth()->user();
        $user->load('player', 'teams');
        $stats = [
            'teams' => $user->teams()->count(),
            'competitions' => $user->competitions()->count(),
            'goals' => $user->player ? MatchEvent::goal()->where('player_id', $user->player->id)->count() : 0,
            'matches' => $user->player
                ? \App\Models\MatchLineup::whereHas('player', fn($q) => $q->where('user_id', $user->id))->count()
                : 0,
        ];

        $recentMatches = Match_::where('status', 'completed')
            ->where(function ($q) use ($user) {
                $teamIds = $user->teams()->pluck('teams.id');
                $q->whereIn('team1_id', $teamIds)->orWhereIn('team2_id', $teamIds);
            })
            ->with(['team1', 'team2', 'competition'])
            ->latest('match_date')
            ->limit(5)
            ->get();

        $playerStats = null;
        if ($user->player) {
            $playerStats = $user->player->seasonStats()->with('competition')->latest('season_year')->get();
        }

        return view('livewire.user.user-dashboard-page', [
            'title' => __('app.page_title_dashboard'),
            'user' => $user,
            'stats' => $stats,
            'recentMatches' => $recentMatches,
            'playerStats' => $playerStats,
        ]);
    }
}
