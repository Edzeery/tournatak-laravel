<?php

namespace App\Enums;

enum MatchStatus: string
{
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Postponed = 'postponed';
    case Cancelled = 'cancelled';
    case Abandoned = 'abandoned';
    case Pending = 'pending';

    public function label(): string
    {
        return __("app.status_{$this->value}");
    }
}
