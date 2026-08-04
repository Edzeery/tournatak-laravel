<?php

namespace App\Listeners;

use App\Events\JudgeAssigned;
use App\Services\NotificationService;

class NotifyJudgeAssigned
{
    public function __construct(private NotificationService $notifier) {}

    public function handle(JudgeAssigned $event): void
    {
        $judge = $event->judge;
        $user = $judge->user;
        $competition = $judge->competition;

        if (! $user || ! $competition) {
            return;
        }

        $this->notifier->notifyUser(
            $user,
            __('app.judge_assigned_title'),
            __('app.judge_assigned_notification', ['competition' => $competition->name]),
            'bi-clipboard-check-fill text-info',
            route('judge.competitions.show', $competition),
            'info',
        );
    }
}
