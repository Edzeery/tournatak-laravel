<?php

namespace App\Listeners;

use App\Enums\ApprovalStatus;
use App\Events\CompetitionStatusChanged;
use App\Models\Competition;
use App\Services\NotificationService;

class NotifyCompetitionStatusChanged
{
    public function __construct(private NotificationService $notifier) {}

    public function handle(CompetitionStatusChanged $event): void
    {
        $competition = $event->competition;

        if ($event->newStatus === ApprovalStatus::Approved->value) {
            $this->notifyOrganizer(
                $competition,
                __('app.competition_approved_notification'),
                'bi-check-circle-fill text-success',
                route('admin.competitions.index'),
                'success',
            );

            return;
        }

        if ($event->newStatus === ApprovalStatus::Rejected->value) {
            $this->notifyOrganizer(
                $competition,
                __('app.competition_rejected_notification'),
                'bi-x-circle-fill text-danger',
                route('admin.competitions.index'),
                'danger',
            );

            return;
        }

        if ($event->newStatus === ApprovalStatus::Pending->value) {
            $this->notifier->createForAdmins(
                __('app.competition_submitted_title'),
                __('app.competition_submitted_notification', ['competition' => $competition->name]),
                'bi-exclamation-triangle-fill text-warning',
                route('admin.competitions.index'),
                'warning',
            );
        }
    }

    private function notifyOrganizer(Competition $competition, string $title, string $icon, string $link, string $type): void
    {
        $organizer = $competition->organizer;

        if ($organizer) {
            $this->notifier->notifyUser($organizer, $title, $competition->name, $icon, $link, $type);

            return;
        }

        $this->notifier->createForRole('organizer', $title, $competition->name, $icon, $link, $type);
    }
}
