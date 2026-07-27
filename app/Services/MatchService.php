<?php

namespace App\Services;

use App\Models\Match_;
use Illuminate\Validation\ValidationException;

class MatchService
{
    public function create(array $data): Match_
    {
        $data['score_team1'] = $data['score_team1'] ?? 0;
        $data['score_team2'] = $data['score_team2'] ?? 0;

        return Match_::create($data);
    }

    public function update(Match_ $match, array $data): Match_
    {
        $match->update($data);

        return $match;
    }

    public function getValidationRules(): array
    {
        return [
            'competition_id' => 'required|exists:competitions,id',
            'team1_id' => 'required|exists:teams,id',
            'team2_id' => 'required|exists:teams,id',
            'match_date' => 'nullable|date',
            'status' => 'required|in:scheduled,in_progress,completed',
        ];
    }

    public function validateSameTeams(int $team1Id, int $team2Id): void
    {
        if ($team1Id === $team2Id) {
            throw ValidationException::withMessages([
                'team2_id' => 'يجب أن يكون الفريقان مختلفين',
            ]);
        }
    }
}
