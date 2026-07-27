<?php

namespace App\Services;

use App\Models\Competition;

class CompetitionService
{
    public function create(array $data): Competition
    {
        $data['organizer_id'] = auth()->id();
        $data['approval_status'] = 'pending';
        $data['status'] = 'draft';

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
}
