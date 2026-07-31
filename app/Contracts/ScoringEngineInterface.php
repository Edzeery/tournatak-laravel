<?php

namespace App\Contracts;

use App\Models\Competition;

interface ScoringEngineInterface
{
    /**
     * Whether this engine handles the given evaluation basis.
     */
    public function supports(string $evaluationBasis): bool;

    /**
     * Ranked participant rows for a competition.
     *
     * @param  array<string, mixed>  $context
     * @return array<int, array<string, mixed>>
     */
    public function calculateRanking(Competition $competition, array $context = []): array;
}
