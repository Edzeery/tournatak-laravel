<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Match_;
use Illuminate\Support\Facades\DB;

class TournamentFormatService
{
    public function generateMatches(Competition $competition): array
    {
        return match ($competition->format) {
            Competition::FORMAT_KNOCKOUT => $this->generateKnockout($competition),
            Competition::FORMAT_GROUPS => $this->generateGroups($competition),
            Competition::FORMAT_LEAGUE_KNOCKOUT => $this->generateGroups($competition),
            Competition::FORMAT_DOUBLE_ELIMINATION => $this->generateDoubleElimination($competition),
            Competition::FORMAT_SWISS => $this->generateSwiss($competition),
            Competition::FORMAT_HOME_AWAY => $this->generateHomeAway($competition),
            default => $this->generateLeague($competition),
        };
    }

    public function getFormatConfig(Competition $competition): array
    {
        $defaults = [
            'rounds' => 1,
            'group_size' => 4,
            'advance_per_group' => 2,
            'third_place_match' => false,
            'swiss_rounds' => 7,
            'home_away_rounds' => 2,
            'scoring' => ScoringEngine::DEFAULT_SCORING,
        ];

        return array_merge($defaults, $competition->format_config ?? []);
    }

    public function generateLeague(Competition $competition): array
    {
        $teams = $competition->teams()->pluck('teams.id')->toArray();
        $matches = [];
        $teamCount = count($teams);

        if ($teamCount < 2) {
            return [];
        }

        for ($i = 0; $i < $teamCount; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $matches[] = [
                    'competition_id' => $competition->id,
                    'team1_id' => $teams[$i],
                    'team2_id' => $teams[$j],
                    'status' => 'scheduled',
                ];
            }
        }

        return $matches;
    }

    public function generateKnockout(Competition $competition): array
    {
        $teams = $competition->teams()->pluck('teams.id')->toArray();
        $config = $this->getFormatConfig($competition);
        $matches = [];

        $teamCount = count($teams);
        if ($teamCount < 2) {
            return [];
        }

        $nextPowerOf2 = pow(2, ceil(log($teamCount, 2)));
        $byes = $nextPowerOf2 - $teamCount;

        $seeded = $teams;
        $roundNumber = 1;
        $roundMatches = [];
        $teamIds = $seeded;

        if ($byes > 0) {
            for ($i = 0; $i < $byes; $i++) {
                $roundMatches[] = [
                    'competition_id' => $competition->id,
                    'team1_id' => $teamIds[$i],
                    'team2_id' => null,
                    'status' => 'scheduled',
                    'round' => $roundNumber,
                    'is_bye' => true,
                ];
            }
            $teamIds = array_slice($teamIds, $byes);
        }

        for ($i = 0; $i < count($teamIds); $i += 2) {
            if (isset($teamIds[$i + 1])) {
                $roundMatches[] = [
                    'competition_id' => $competition->id,
                    'team1_id' => $teamIds[$i],
                    'team2_id' => $teamIds[$i + 1],
                    'status' => 'scheduled',
                    'round' => $roundNumber,
                    'is_bye' => false,
                ];
            }
        }

        $matches = array_merge($matches, $roundMatches);

        $rounds = (int) log($nextPowerOf2, 2);
        for ($r = 2; $r <= $rounds; $r++) {
            $teamsInRound = $nextPowerOf2 / pow(2, $r - 1);
            for ($m = 0; $m < $teamsInRound / 2; $m++) {
                $matches[] = [
                    'competition_id' => $competition->id,
                    'team1_id' => null,
                    'team2_id' => null,
                    'status' => 'scheduled',
                    'round' => $r,
                    'is_bye' => false,
                ];
            }
        }

        if ($config['third_place_match']) {
            $matches[] = [
                'competition_id' => $competition->id,
                'team1_id' => null,
                'team2_id' => null,
                'status' => 'scheduled',
                'round' => $rounds + 1,
                'is_bye' => false,
                'is_third_place' => true,
            ];
        }

        return $matches;
    }

    public function generateGroups(Competition $competition): array
    {
        $teams = $competition->teams()->pluck('teams.id')->toArray();
        $config = $this->getFormatConfig($competition);
        $groupSize = $config['group_size'];
        $matches = [];

        if (count($teams) < 2) {
            return [];
        }

        $groups = array_chunk($teams, $groupSize);
        $groupNames = range('A', 'Z');

        foreach ($groups as $groupIndex => $groupTeams) {
            $groupName = $groupNames[$groupIndex] ?? "G{$groupIndex}";
            for ($i = 0; $i < count($groupTeams); $i++) {
                for ($j = $i + 1; $j < count($groupTeams); $j++) {
                    $matches[] = [
                        'competition_id' => $competition->id,
                        'team1_id' => $groupTeams[$i],
                        'team2_id' => $groupTeams[$j],
                        'status' => 'scheduled',
                        'group' => $groupName,
                        'stage' => 'group',
                    ];
                }
            }
        }

        $advanceCount = $config['advance_per_group'] * count($groups);
        $knockoutRounds = (int) ceil(log(max($advanceCount, 2), 2));
        $knockoutTeams = pow(2, $knockoutRounds);

        for ($r = 1; $r <= $knockoutRounds; $r++) {
            $matchCount = $knockoutTeams / pow(2, $r);
            for ($m = 0; $m < $matchCount; $m++) {
                $matches[] = [
                    'competition_id' => $competition->id,
                    'team1_id' => null,
                    'team2_id' => null,
                    'status' => 'scheduled',
                    'round' => $r,
                    'stage' => 'knockout',
                ];
            }
        }

        return $matches;
    }

    public function generateHomeAway(Competition $competition): array
    {
        $teams = $competition->teams()->pluck('teams.id')->toArray();
        $matches = [];
        $teamCount = count($teams);

        if ($teamCount < 2) {
            return [];
        }

        for ($i = 0; $i < $teamCount; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $matches[] = [
                    'competition_id' => $competition->id,
                    'team1_id' => $teams[$i],
                    'team2_id' => $teams[$j],
                    'status' => 'scheduled',
                    'leg' => 1,
                ];
                $matches[] = [
                    'competition_id' => $competition->id,
                    'team1_id' => $teams[$j],
                    'team2_id' => $teams[$i],
                    'status' => 'scheduled',
                    'leg' => 2,
                ];
            }
        }

        return $matches;
    }

    public function generateDoubleElimination(Competition $competition): array
    {
        $teams = $competition->teams()->pluck('teams.id')->toArray();
        $matches = [];
        $teamCount = count($teams);

        if ($teamCount < 2) {
            return [];
        }

        $nextPowerOf2 = pow(2, ceil(log($teamCount, 2)));

        for ($r = 1; $r <= (int) log($nextPowerOf2, 2); $r++) {
            $matchCount = $nextPowerOf2 / pow(2, $r);
            for ($m = 0; $m < $matchCount; $m++) {
                $matches[] = [
                    'competition_id' => $competition->id,
                    'team1_id' => null,
                    'team2_id' => null,
                    'status' => 'scheduled',
                    'round' => $r,
                    'bracket' => 'winners',
                ];
            }
        }

        $losersRounds = (int) log($nextPowerOf2, 2) * 2 - 1;
        for ($r = 1; $r <= $losersRounds; $r++) {
            $matches[] = [
                'competition_id' => $competition->id,
                'team1_id' => null,
                'team2_id' => null,
                'status' => 'scheduled',
                'round' => $r,
                'bracket' => 'losers',
            ];
        }

        $matches[] = [
            'competition_id' => $competition->id,
            'team1_id' => null,
            'team2_id' => null,
            'status' => 'scheduled',
            'round' => 99,
            'bracket' => 'grand_final',
        ];

        return $matches;
    }

    public function generateSwiss(Competition $competition): array
    {
        $teams = $competition->teams()->pluck('teams.id')->toArray();
        $config = $this->getFormatConfig($competition);
        $matches = [];

        if (count($teams) < 2) {
            return [];
        }

        $rounds = $config['swiss_rounds'];
        for ($r = 1; $r <= $rounds; $r++) {
            for ($i = 0; $i < count($teams); $i += 2) {
                if (isset($teams[$i + 1])) {
                    $matches[] = [
                        'competition_id' => $competition->id,
                        'team1_id' => $teams[$i],
                        'team2_id' => $teams[$i + 1],
                        'status' => 'scheduled',
                        'round' => $r,
                    ];
                }
            }
            shuffle($teams);
        }

        return $matches;
    }

    public function createMatches(Competition $competition): int
    {
        $schedule = $this->generateMatches($competition);

        if (empty($schedule)) {
            return 0;
        }

        $created = 0;
        DB::transaction(function () use ($schedule, &$created) {
            foreach ($schedule as $matchData) {
                $round = $matchData['round'] ?? null;
                $group = $matchData['group'] ?? null;
                $stage = $matchData['stage'] ?? null;
                $leg = $matchData['leg'] ?? null;
                $bracket = $matchData['bracket'] ?? null;
                $isBye = $matchData['is_bye'] ?? false;
                $isThirdPlace = $matchData['is_third_place'] ?? false;

                unset($matchData['round'], $matchData['group'], $matchData['stage'],
                    $matchData['leg'], $matchData['bracket'], $matchData['is_bye'],
                    $matchData['is_third_place']);

                $extraData = array_filter([
                    'round' => $round,
                    'group_name' => $group,
                    'stage' => $stage,
                    'leg' => $leg,
                    'bracket' => $bracket,
                    'is_bye' => $isBye ? true : null,
                    'is_third_place' => $isThirdPlace ? true : null,
                ], fn ($v) => $v !== null);

                Match_::create(array_merge($matchData, $extraData));
                $created++;
            }
        });

        return $created;
    }

    /**
     * Domain-neutral round descriptors derived from the competition format.
     *
     * @return array<int, array<string, mixed>>
     */
    public function generateRounds(Competition $competition): array
    {
        return match ($competition->format) {
            Competition::FORMAT_KNOCKOUT => $this->knockoutRounds($competition),
            Competition::FORMAT_GROUPS, Competition::FORMAT_LEAGUE_KNOCKOUT => $this->groupRounds($competition),
            Competition::FORMAT_DOUBLE_ELIMINATION => $this->doubleEliminationRounds($competition),
            Competition::FORMAT_SWISS => $this->numberedRounds($competition, (int) $this->getFormatConfig($competition)['swiss_rounds']),
            Competition::FORMAT_HOME_AWAY => $this->numberedRounds($competition, (int) $this->getFormatConfig($competition)['home_away_rounds']),
            default => $this->numberedRounds($competition, 1),
        };
    }

    public function createRounds(Competition $competition): int
    {
        $descriptors = $this->generateRounds($competition);

        if (empty($descriptors)) {
            return 0;
        }

        $existingNumbers = CompetitionRound::where('competition_id', $competition->id)->pluck('number')->all();

        $created = 0;
        DB::transaction(function () use ($competition, $descriptors, $existingNumbers, &$created) {
            foreach ($descriptors as $descriptor) {
                if (in_array($descriptor['round_number'], $existingNumbers, true)) {
                    continue;
                }

                CompetitionRound::create([
                    'competition_id' => $competition->id,
                    'name' => $descriptor['name'],
                    'number' => $descriptor['round_number'],
                    'status' => CompetitionRound::STATUS_SCHEDULED,
                ]);
                $created++;
            }
        });

        return $created;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function numberedRounds(Competition $competition, int $count): array
    {
        if ($count < 1) {
            return [];
        }

        $rounds = [];

        for ($i = 1; $i <= $count; $i++) {
            $rounds[] = [
                'competition_id' => $competition->id,
                'round_number' => $i,
                'name' => __('app.round', ['number' => $i]),
                'stage' => null,
            ];
        }

        return $rounds;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function knockoutRounds(Competition $competition): array
    {
        $teamCount = $competition->teams()->count();

        if ($teamCount < 2) {
            return [];
        }

        $nextPowerOf2 = pow(2, ceil(log($teamCount, 2)));

        return $this->numberedRounds($competition, (int) log($nextPowerOf2, 2));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function groupRounds(Competition $competition): array
    {
        $config = $this->getFormatConfig($competition);
        $teamCount = $competition->teams()->count();

        $rounds = [[
            'competition_id' => $competition->id,
            'round_number' => 1,
            'name' => __('app.round_group_stage'),
            'stage' => 'group',
        ]];

        if ($teamCount < 2) {
            return $rounds;
        }

        $groupCount = max(1, (int) ceil($teamCount / $config['group_size']));
        $advanceCount = $config['advance_per_group'] * $groupCount;
        $knockoutRounds = (int) ceil(log(max($advanceCount, 2), 2));

        for ($i = 1; $i <= $knockoutRounds; $i++) {
            $rounds[] = [
                'competition_id' => $competition->id,
                'round_number' => $i + 1,
                'name' => __('app.round_knockout_stage', ['round' => $i]),
                'stage' => 'knockout',
            ];
        }

        return $rounds;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function doubleEliminationRounds(Competition $competition): array
    {
        $teamCount = $competition->teams()->count();

        if ($teamCount < 2) {
            return [];
        }

        $nextPowerOf2 = pow(2, ceil(log($teamCount, 2)));
        $base = (int) log($nextPowerOf2, 2);
        $count = $base + (2 * $base - 1) + 1;

        return $this->numberedRounds($competition, $count);
    }
}
