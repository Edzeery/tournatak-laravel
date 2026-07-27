<?php

namespace App\Services;

use App\Models\Team;

class TeamService
{
    public function create(array $data): Team
    {
        return Team::create($data);
    }

    public function update(Team $team, array $data): Team
    {
        $team->update($data);

        return $team;
    }

    public function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:teams,name',
            'captain_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|string|max:255',
            'points' => 'integer|min:0',
        ];
    }
}
