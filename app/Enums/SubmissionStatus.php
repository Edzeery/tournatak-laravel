<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Pending = 'pending';

    case UnderReview = 'under_review';

    case Approved = 'approved';

    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('app.status_pending'),
            self::UnderReview => __('app.status_under_review'),
            self::Approved => __('app.status_approved'),
            self::Rejected => __('app.status_rejected'),
        };
    }
}
