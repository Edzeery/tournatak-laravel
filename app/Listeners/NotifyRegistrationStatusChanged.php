<?php

namespace App\Listeners;

use App\Events\RegistrationStatusChanged;
use App\Models\Registration;
use App\Services\NotificationService;

class NotifyRegistrationStatusChanged
{
    public function __construct(private NotificationService $notifier) {}

    public function handle(RegistrationStatusChanged $event): void
    {
        $registration = $event->registration;
        $approved = $event->newStatus === Registration::STATUS_APPROVED;

        $title = $approved
            ? __('app.registration_approved_notification')
            : __('app.registration_rejected_notification');

        $icon = $approved
            ? 'bi-check-circle-fill text-success'
            : 'bi-x-circle-fill text-danger';

        $type = $approved ? 'success' : 'danger';

        $competition = $registration->competition;
        $message = $competition?->name;

        $user = $registration->user ?? $registration->team?->captain;
        if ($user) {
            $this->notifier->notifyUser(
                $user,
                $title,
                $message,
                $icon,
                route('user.registrations'),
                $type,
            );
        }

        $organizer = $competition?->organizer;
        if ($organizer) {
            $this->notifier->notifyUser(
                $organizer,
                $title,
                $message,
                $icon,
                route('admin.registrations.index'),
                $type,
            );
        } else {
            $this->notifier->createForRole(
                'organizer',
                $title,
                $message,
                $icon,
                route('admin.registrations.index'),
                $type,
            );
        }
    }
}
