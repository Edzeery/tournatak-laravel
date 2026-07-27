<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Competition;
use App\Models\Team;
use App\Models\Player;
use App\Models\Match_;
use App\Models\Goal;
use App\Models\TeamStaff;
use App\Models\TeamMedicalRecord;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class DashboardPage extends Component
{
    public function render()
    {
        $stats = [
            'users' => User::count(),
            'competitions' => Competition::count(),
            'teams' => Team::count(),
            'players' => Player::count(),
            'matches' => Match_::count(),
            'matches_completed' => Match_::where('status', 'completed')->count(),
            'matches_scheduled' => Match_::where('status', 'scheduled')->count(),
            'goals' => Goal::count(),
            'staff' => TeamStaff::where('is_active', true)->count(),
            'injuries' => TeamMedicalRecord::where('status', 'active')->count(),
        ];

        $recentMatches = Match_::with(['team1', 'team2', 'competition'])
            ->latest('match_date')
            ->limit(5)
            ->get();

        $topScorers = Goal::select('player_id', DB::raw('count(*) as goals'))
            ->with('player.user', 'player.team')
            ->groupBy('player_id')
            ->orderByDesc('goals')
            ->limit(5)
            ->get();

        return view('livewire.admin.dashboard-page', [
            'title' => 'لوحة التحكم',
            'stats' => $stats,
            'activities' => Activity::with('user')->latest()->limit(10)->get(),
            'recentMatches' => $recentMatches,
            'topScorers' => $topScorers,
        ]);
    }
}
