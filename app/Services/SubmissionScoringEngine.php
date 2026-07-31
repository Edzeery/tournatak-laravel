<?php

namespace App\Services;

use App\Contracts\ScoringEngineInterface;
use App\Enums\CompetitionEvaluationBasis;
use App\Models\Competition;
use App\Models\Submission;

class SubmissionScoringEngine implements ScoringEngineInterface
{
    const DEFAULT_CONFIG = [
        'max_score' => 100,
        'aggregation' => 'average',
    ];

    const AGGREGATION_AVERAGE = 'average';

    const AGGREGATION_TOTAL = 'total';

    const AGGREGATION_MIN = 'min';

    const AGGREGATION_MAX = 'max';

    public function supports(string $evaluationBasis): bool
    {
        return $evaluationBasis === CompetitionEvaluationBasis::Submission->value;
    }

    public function getConfig(Competition $competition): array
    {
        return array_merge(self::DEFAULT_CONFIG, $competition->format_config['scoring'] ?? []);
    }

    public function calculateRanking(Competition $competition, array $context = []): array
    {
        $aggregation = $context['aggregation'] ?? $this->getConfig($competition)['aggregation'];

        $submissions = $context['submissions'] ?? $competition->submissions()
            ->with(['judgeScores', 'team', 'user', 'player'])
            ->get();

        $rows = $submissions->map(function (Submission $submission) use ($aggregation) {
            return [
                'submission_id' => $submission->id,
                'participant_name' => $submission->getParticipantName(),
                'score' => $this->aggregateScore($submission, $aggregation),
                'scores_count' => $submission->judgeScores->count(),
            ];
        });

        return $rows->sortByDesc('score')->values()->all();
    }

    public function aggregateScore(Submission $submission, string $aggregation = self::AGGREGATION_AVERAGE): float
    {
        $scores = $submission->judgeScores()->pluck('score');

        if ($scores->isEmpty()) {
            return 0.0;
        }

        return match ($aggregation) {
            self::AGGREGATION_TOTAL => round((float) $scores->sum(), 2),
            self::AGGREGATION_MIN => round((float) $scores->min(), 2),
            self::AGGREGATION_MAX => round((float) $scores->max(), 2),
            default => round((float) $scores->avg(), 2),
        };
    }

    public function maxScore(Competition $competition): float
    {
        return (float) ($this->getConfig($competition)['max_score'] ?? self::DEFAULT_CONFIG['max_score']);
    }
}
