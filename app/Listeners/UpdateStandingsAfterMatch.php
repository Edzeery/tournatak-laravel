<?php

namespace App\Listeners;

use App\Events\MatchCompleted;
use App\Models\TeamSeasonStat;
use App\Services\NotificationService;
use App\Services\StandingService;
use Illuminate\Support\Facades\Cache;

class UpdateStandingsAfterMatch
{
    public function __construct(
        protected StandingService $standingService,
        protected NotificationService $notifier,
    ) {}

    public function handle(MatchCompleted $event): void
    {
        $match = $event->match;
        $competition = $match->competition;

        if (!$competition) {
            return;
        }

        $score1 = $match->score_team1 ?? 0;
        $score2 = $match->score_team2 ?? 0;

        foreach ([$match->team1_id, $match->team2_id] as $teamId) {
            $stat = TeamSeasonStat::firstOrNew([
                'team_id' => $teamId,
                'competition_id' => $competition->id,
                'season_year' => date('Y'),
            ]);

            $stat->matches_played = ($stat->matches_played ?? 0) + 1;

            if ($teamId === $match->team1_id) {
                $stat->goals_for = ($stat->goals_for ?? 0) + $score1;
                $stat->goals_against = ($stat->goals_against ?? 0) + $score2;
                if ($score1 > $score2) {
                    $stat->wins = ($stat->wins ?? 0) + 1;
                    $stat->points = ($stat->points ?? 0) + 3;
                } elseif ($score1 < $score2) {
                    $stat->losses = ($stat->losses ?? 0) + 1;
                } else {
                    $stat->draws = ($stat->draws ?? 0) + 1;
                    $stat->points = ($stat->points ?? 0) + 1;
                }
            } else {
                $stat->goals_for = ($stat->goals_for ?? 0) + $score2;
                $stat->goals_against = ($stat->goals_against ?? 0) + $score1;
                if ($score2 > $score1) {
                    $stat->wins = ($stat->wins ?? 0) + 1;
                    $stat->points = ($stat->points ?? 0) + 3;
                } elseif ($score2 < $score1) {
                    $stat->losses = ($stat->losses ?? 0) + 1;
                } else {
                    $stat->draws = ($stat->draws ?? 0) + 1;
                    $stat->points = ($stat->points ?? 0) + 1;
                }
            }

            $stat->save();
        }

        Cache::forget("standings_{$competition->id}");

        $team1 = $match->team1;
        $team2 = $match->team2;
        $summary = "{$team1->name} {$score1}-{$score2} {$team2->name}";

        $this->notifier->createForAdmins(
            title: __('app.match_completed'),
            message: __('app.match_completed_notification', ['match' => $summary, 'competition' => $competition->name]),
            icon: 'bi-trophy-fill text-warning',
            link: route('admin.matches.index'),
            type: 'success',
        );
    }
}
