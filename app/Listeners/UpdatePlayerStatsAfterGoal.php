<?php

namespace App\Listeners;

use App\Events\GoalScored;
use App\Models\PlayerSeasonStat;
use App\Services\NotificationService;

class UpdatePlayerStatsAfterGoal
{
    public function __construct(
        protected NotificationService $notifier,
    ) {}

    public function handle(GoalScored $event): void
    {
        $matchEvent = $event->event;
        $match = $matchEvent->match;

        if (! $match || ! $match->competition) {
            return;
        }

        if (in_array($matchEvent->event_type, ['goal', 'penalty_scored']) && $matchEvent->player_id) {
            $stat = PlayerSeasonStat::firstOrNew([
                'player_id' => $matchEvent->player_id,
                'competition_id' => $match->competition_id,
                'season_year' => date('Y'),
            ]);

            $stat->goals = ($stat->goals ?? 0) + 1;
            $stat->save();
        }

        $player = $matchEvent->player;
        $team = $matchEvent->team;
        $this->notifier->createForAdmins(
            title: __('app.goal_scored'),
            message: __('app.goal_scored_notification', [
                'player' => $player?->user?->name ?? $matchEvent->player_id,
                'team' => $team?->name ?? '',
                'minute' => $matchEvent->minute,
            ]),
            icon: 'bi-circle-fill text-success',
            link: route('admin.matches.index'),
            type: 'success',
        );
    }
}
