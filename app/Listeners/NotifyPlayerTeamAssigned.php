<?php

namespace App\Listeners;

use App\Events\PlayerTeamAssigned;
use App\Models\Team;
use App\Services\NotificationService;
use Illuminate\Support\Collection;

class NotifyPlayerTeamAssigned
{
    public function __construct(private NotificationService $notifier) {}

    public function handle(PlayerTeamAssigned $event): void
    {
        $player = $event->player;
        $playerUser = $player->user;
        $team = $player->team;

        if (! $playerUser || ! $team) {
            return;
        }

        $title = __('app.player_assigned_title');

        foreach ($this->teamRecipients($team)->push($playerUser->id)->unique() as $userId) {
            $this->notifier->create(
                $userId,
                $title,
                __('app.player_joined_notification', [
                    'player' => $player->name,
                    'team' => $team->name,
                ]),
                'bi-person-plus-fill text-success',
                route('teams.show', $team->id),
                'success',
            );
        }

        if ($event->previousTeamId && $event->previousTeamId !== $team->id) {
            $oldTeam = Team::find($event->previousTeamId);

            if ($oldTeam) {
                foreach ($this->teamRecipients($oldTeam) as $userId) {
                    $this->notifier->create(
                        $userId,
                        $title,
                        __('app.player_left_notification', [
                            'player' => $player->name,
                            'team' => $oldTeam->name,
                        ]),
                        'bi-person-x-fill text-warning',
                        route('teams.show', $oldTeam->id),
                        'warning',
                    );
                }
            }
        }
    }

    private function teamRecipients(Team $team): Collection
    {
        $userIds = collect([$team->captain_id])->filter();

        return $userIds->merge($team->activeStaff()->pluck('user_id'));
    }
}
