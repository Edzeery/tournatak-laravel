<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Competition;
use App\Models\Team;
use App\Models\Player;
use App\Models\Match_;
use App\Models\MatchEvent;
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
            'goals' => MatchEvent::goal()->count(),
            'staff' => TeamStaff::where('is_active', true)->count(),
            'injuries' => TeamMedicalRecord::where('status', 'active')->count(),
        ];

        $recentMatches = Match_::with(['team1', 'team2', 'competition'])
            ->latest('match_date')
            ->limit(5)
            ->get();

        $matchStatuses = Match_::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $topScorers = MatchEvent::goal()
            ->select('player_id', DB::raw('count(*) as goals'))
            ->with('player.user', 'player.team')
            ->groupBy('player_id')
            ->orderByDesc('goals')
            ->limit(5)
            ->get();

        $monthlyGoals = MatchEvent::goal()
            ->select(
                DB::raw('MONTH(matches.match_date) as month'),
                DB::raw('count(*) as total')
            )
            ->join('matches', 'match_events.match_id', '=', 'matches.id')
            ->whereYear('matches.match_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $competitionStats = Competition::select(
                DB::raw('CASE status WHEN "draft" THEN 0 WHEN "upcoming" THEN 1 WHEN "ongoing" THEN 2 WHEN "completed" THEN 3 END as sort_order'),
                'status',
                DB::raw('count(*) as total')
            )
            ->groupBy('status')
            ->orderBy('sort_order')
            ->pluck('total', 'status');

        return view('livewire.admin.dashboard-page', [
            'title' => __('app.page_title_dashboard'),
            'stats' => $stats,
            'activities' => Activity::with('user')->latest()->limit(10)->get(),
            'recentMatches' => $recentMatches,
            'topScorers' => $topScorers,
            'matchStatuses' => $matchStatuses,
            'monthlyGoals' => $monthlyGoals,
            'competitionStats' => $competitionStats,
        ]);
    }
}
