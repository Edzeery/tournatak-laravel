<?php

namespace App\Listeners;

use App\Enums\SubmissionStatus;
use App\Events\SubmissionStatusChanged;
use App\Models\Submission;
use App\Models\User;
use App\Services\NotificationService;

class NotifySubmissionStatusChanged
{
    public function __construct(private NotificationService $notifier) {}

    public function handle(SubmissionStatusChanged $event): void
    {
        if ($event->oldStatus === $event->newStatus) {
            return;
        }

        $submission = $event->submission;

        $approved = $event->newStatus === SubmissionStatus::Approved->value;

        if (! $approved && $event->newStatus !== SubmissionStatus::Rejected->value) {
            return;
        }

        $recipient = $this->recipientFor($submission);

        if (! $recipient) {
            return;
        }

        $competition = $submission->competition;

        $this->notifier->notifyUser(
            $recipient,
            $approved
                ? __('app.submission_approved_notification')
                : __('app.submission_rejected_notification'),
            $submission->title,
            $approved
                ? 'bi-check-circle-fill text-success'
                : 'bi-x-circle-fill text-danger',
            $competition ? route('competitions.show', $competition) : null,
            $approved ? 'success' : 'danger',
        );
    }

    private function recipientFor(Submission $submission): ?User
    {
        if ($submission->isTeamSubmission()) {
            return $submission->team?->captain;
        }

        return $submission->user ?? $submission->player?->user;
    }
}
