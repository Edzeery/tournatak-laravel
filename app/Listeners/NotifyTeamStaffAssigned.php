<?php

namespace App\Listeners;

use App\Events\TeamStaffAssigned;
use App\Services\NotificationService;

class NotifyTeamStaffAssigned
{
    public function __construct(private NotificationService $notifier) {}

    public function handle(TeamStaffAssigned $event): void
    {
        $staff = $event->teamStaff;
        $user = $staff->user;
        $team = $staff->team;

        if (! $user || ! $team) {
            return;
        }

        $this->notifier->notifyUser(
            $user,
            __('app.team_staff_assigned_title'),
            __('app.team_staff_assigned_notification', [
                'team' => $team->name,
                'role' => $staff->staff_role_label,
            ]),
            'bi-person-plus-fill text-info',
            route('teams.show', $team->id),
            'info',
        );
    }
}
