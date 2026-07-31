<?php

namespace App\Services;

use App\Contracts\ScoringEngineInterface;
use App\Enums\CompetitionEvaluationBasis;
use App\Models\Competition;

class SportsScoringEngine implements ScoringEngineInterface
{
    public function __construct(
        protected StandingService $standingService,
    ) {}

    public function supports(string $evaluationBasis): bool
    {
        return $evaluationBasis === CompetitionEvaluationBasis::Match->value;
    }

    public function calculateRanking(Competition $competition, array $context = []): array
    {
        return $this->standingService->calculate($competition);
    }
}
