<?php

namespace App\Services;

use App\Models\Competition;

class ScoringEngine
{
    const DEFAULT_SCORING = [
        'win' => 3,
        'draw' => 1,
        'loss' => 0,
        'tiebreakers' => ['goal_difference', 'goals_for'],
    ];

    public function getConfig(Competition $competition): array
    {
        $formatConfig = $competition->format_config ?? [];

        return array_merge(self::DEFAULT_SCORING, $formatConfig['scoring'] ?? []);
    }

    public function getWinPoints(Competition $competition): int
    {
        return $this->getConfig($competition)['win'];
    }

    public function getDrawPoints(Competition $competition): int
    {
        return $this->getConfig($competition)['draw'];
    }

    public function getLossPoints(Competition $competition): int
    {
        return $this->getConfig($competition)['loss'];
    }

    public function getTiebreakers(Competition $competition): array
    {
        return $this->getConfig($competition)['tiebreakers'];
    }

    public function calculatePoints(Competition $competition, int $goalsFor, int $goalsAgainst): int
    {
        if ($goalsFor > $goalsAgainst) {
            return $this->getWinPoints($competition);
        }
        if ($goalsFor < $goalsAgainst) {
            return $this->getLossPoints($competition);
        }

        return $this->getDrawPoints($competition);
    }

    public function sortStandings(array $standings, Competition $competition): array
    {
        $tiebreakers = $this->getTiebreakers($competition);

        usort($standings, function (array $a, array $b) use ($tiebreakers) {
            foreach ($tiebreakers as $rule) {
                $result = match ($rule) {
                    'points' => $b['points'] - $a['points'],
                    'goal_difference' => ($b['goals_for'] - $b['goals_against']) - ($a['goals_for'] - $a['goals_against']),
                    'goals_for' => $b['goals_for'] - $a['goals_for'],
                    'goals_against' => $a['goals_against'] - $b['goals_against'],
                    'wins' => $b['won'] - $a['won'],
                    'head_to_head' => 0, // requires full match data; currently unused in defaults
                    default => 0,
                };

                if ($result !== 0) {
                    return $result;
                }
            }

            return 0;
        });

        return $standings;
    }
}
