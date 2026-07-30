<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Match_;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class StandingService
{
    public function __construct(
        protected ScoringEngine $scoringEngine,
    ) {}

    public function calculate(Competition $competition): array
    {
        $completedMatches = $competition->matches()
            ->where('status', 'completed')
            ->with(['team1', 'team2'])
            ->get();

        $teams = $competition->teams()->with(['seasonStats' => function ($q) use ($competition) {
            $q->where('competition_id', $competition->id);
        }])->get();

        $standings = [];

        foreach ($teams as $team) {
            $standings[$team->id] = $this->initializeStanding($team, $competition);
        }

        foreach ($completedMatches as $match) {
            $this->processMatch($standings, $match, $competition);
        }

        $standings = $this->scoringEngine->sortStandings($standings, $competition);

        return array_values($standings);
    }

    protected function initializeStanding(Team $team, Competition $competition): array
    {
        return [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'team_logo' => $team->logo,
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
            'form' => [],
            'clean_sheets' => 0,
        ];
    }

    protected function processMatch(array &$standings, Match_ $match, Competition $competition): void
    {
        $t1Id = $match->team1_id;
        $t2Id = $match->team2_id;

        if (! isset($standings[$t1Id]) || ! isset($standings[$t2Id])) {
            return;
        }

        $score1 = $match->score_team1 ?? 0;
        $score2 = $match->score_team2 ?? 0;

        $points1 = $this->scoringEngine->calculatePoints($competition, $score1, $score2);
        $points2 = $this->scoringEngine->calculatePoints($competition, $score2, $score1);

        $standings[$t1Id]['played']++;
        $standings[$t2Id]['played']++;
        $standings[$t1Id]['goals_for'] += $score1;
        $standings[$t1Id]['goals_against'] += $score2;
        $standings[$t2Id]['goals_for'] += $score2;
        $standings[$t2Id]['goals_against'] += $score1;

        if ($points1 > $points2) {
            $standings[$t1Id]['won']++;
            $standings[$t1Id]['form'][] = 'W';
            $standings[$t2Id]['lost']++;
            $standings[$t2Id]['form'][] = 'L';
            if ($score2 === 0) {
                $standings[$t1Id]['clean_sheets']++;
            }
        } elseif ($points1 < $points2) {
            $standings[$t2Id]['won']++;
            $standings[$t2Id]['form'][] = 'W';
            $standings[$t1Id]['lost']++;
            $standings[$t1Id]['form'][] = 'L';
            if ($score1 === 0) {
                $standings[$t2Id]['clean_sheets']++;
            }
        } else {
            $standings[$t1Id]['drawn']++;
            $standings[$t2Id]['drawn']++;
            $standings[$t1Id]['form'][] = 'D';
            $standings[$t2Id]['form'][] = 'D';
            if ($score1 === 0) {
                $standings[$t1Id]['clean_sheets']++;
                $standings[$t2Id]['clean_sheets']++;
            }
        }

        $standings[$t1Id]['points'] += $points1;
        $standings[$t2Id]['points'] += $points2;

        $standings[$t1Id]['goal_difference'] = $standings[$t1Id]['goals_for'] - $standings[$t1Id]['goals_against'];
        $standings[$t2Id]['goal_difference'] = $standings[$t2Id]['goals_for'] - $standings[$t2Id]['goals_against'];
    }

    public function getTopScorers(Competition $competition, int $limit = 10): array
    {
        $matchIds = $competition->matches()->where('status', 'completed')->pluck('id');

        return DB::table('match_events')
            ->join('players', 'match_events.player_id', '=', 'players.id')
            ->join('users', 'players.user_id', '=', 'users.id')
            ->join('teams', 'players.team_id', '=', 'teams.id')
            ->whereIn('match_events.match_id', $matchIds)
            ->whereIn('match_events.event_type', ['goal', 'penalty_scored'])
            ->select(
                'players.id as player_id',
                'users.name as player_name',
                'teams.id as team_id',
                'teams.name as team_name',
                DB::raw('COUNT(*) as total_goals')
            )
            ->groupBy('players.id', 'users.name', 'teams.id', 'teams.name')
            ->orderBy('total_goals', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getAssists(Competition $competition, int $limit = 10): array
    {
        $matchIds = $competition->matches()->where('status', 'completed')->pluck('id');

        return DB::table('match_events')
            ->join('players', 'match_events.player_id', '=', 'players.id')
            ->join('users', 'players.user_id', '=', 'users.id')
            ->join('teams', 'players.team_id', '=', 'teams.id')
            ->whereIn('match_events.match_id', $matchIds)
            ->where('match_events.event_type', 'assist')
            ->select(
                'players.id as player_id',
                'users.name as player_name',
                'teams.id as team_id',
                'teams.name as team_name',
                DB::raw('COUNT(*) as total_assists')
            )
            ->groupBy('players.id', 'users.name', 'teams.id', 'teams.name')
            ->orderBy('total_assists', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getYellowCards(Competition $competition, int $limit = 10): array
    {
        return $this->getCardLeaders($competition, ['yellow_card', 'second_yellow'], $limit);
    }

    public function getRedCards(Competition $competition, int $limit = 10): array
    {
        return $this->getCardLeaders($competition, ['red_card'], $limit);
    }

    protected function getCardLeaders(Competition $competition, array $eventTypes, int $limit): array
    {
        $matchIds = $competition->matches()->where('status', 'completed')->pluck('id');

        return DB::table('match_events')
            ->join('players', 'match_events.player_id', '=', 'players.id')
            ->join('users', 'players.user_id', '=', 'users.id')
            ->join('teams', 'players.team_id', '=', 'teams.id')
            ->whereIn('match_events.match_id', $matchIds)
            ->whereIn('match_events.event_type', $eventTypes)
            ->select(
                'players.id as player_id',
                'users.name as player_name',
                'teams.id as team_id',
                'teams.name as team_name',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('players.id', 'users.name', 'teams.id', 'teams.name')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
