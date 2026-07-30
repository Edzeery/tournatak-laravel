<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Sport;

class CompetitionService
{
    public function create(array $data): Competition
    {
        $profile = $data['competition_profile'] ?? Competition::PROFILE_OFFICIAL;
        $data['organizer_id'] = auth()->id();
        $data['approval_status'] = $profile === Competition::PROFILE_CASUAL ? 'approved' : 'pending';
        $data['status'] = 'draft';

        if (empty($data['sport_id'])) {
            $data['sport_id'] = Sport::where('slug', 'football')->value('id');
        }

        return Competition::create($data);
    }

    public function update(Competition $competition, array $data): Competition
    {
        $competition->update($data);

        return $competition;
    }

    public function approve(Competition $competition): Competition
    {
        $competition->update(['approval_status' => 'approved']);

        return $competition;
    }

    public function reject(Competition $competition): Competition
    {
        $competition->update(['approval_status' => 'rejected']);

        return $competition;
    }

    public function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:competition_types,id',
            'subtype_id' => 'required|exists:competition_subtypes,id',
        ];
    }

    public function getCasualValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'format' => 'required|string|in:knockout,groups,league',
        ];
    }
}
