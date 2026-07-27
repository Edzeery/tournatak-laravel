<?php

namespace App\Http\Controllers\Api;

use App\Models\Competition;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = [
            'competitions' => Competition::count(),
            'teams' => Team::count(),
            'players' => Player::count(),
        ];

        $activeCompetitions = Competition::where('approval_status', 'approved')
            ->where('status', 'ongoing')
            ->with('organizer')
            ->latest()
            ->limit(6)
            ->get(['id', 'name', 'description', 'status', 'start_date', 'organizer_id']);

        $latestTeams = Team::latest()
            ->limit(6)
            ->get(['id', 'name', 'logo', 'points']);

        $topPlayers = Player::with(['user', 'team'])
            ->withCount('goals')
            ->orderByDesc('goals_count')
            ->limit(6)
            ->get(['id', 'user_id', 'team_id', 'number', 'position', 'image']);

        return response()->json([
            'stats' => $stats,
            'active_competitions' => $activeCompetitions,
            'latest_teams' => $latestTeams,
            'top_players' => $topPlayers,
        ]);
    }
}
