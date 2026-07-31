<?php

namespace App\Enums;

enum CompetitionEvaluationBasis: string
{
    case Match = 'match';

    case Submission = 'submission';

    public function label(): string
    {
        return match ($this) {
            self::Match => __('app.evaluation_basis_match'),
            self::Submission => __('app.evaluation_basis_submission'),
        };
    }
}
