<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\Sport;
use Illuminate\Support\Facades\DB;

class CompetitionService
{
    public function create(array $data): Competition
    {
        return DB::transaction(function () use ($data) {
            $profile = $data['competition_profile'] ?? Competition::PROFILE_OFFICIAL;
            $data['organizer_id'] = auth()->id();
            $data['approval_status'] = $profile === Competition::PROFILE_CASUAL ? ApprovalStatus::Approved->value : ApprovalStatus::Pending->value;
            $data['status'] = CompetitionStatus::Draft->value;

            if (empty($data['sport_id'])) {
                $data['sport_id'] = Sport::where('slug', 'football')->value('id');
            }

            return Competition::create($data);
        });
    }

    public function update(Competition $competition, array $data): Competition
    {
        $competition->update($data);

        return $competition;
    }

    public function approve(Competition $competition): Competition
    {
        $competition->update(['approval_status' => ApprovalStatus::Approved->value]);

        return $competition;
    }

    public function reject(Competition $competition): Competition
    {
        $competition->update(['approval_status' => ApprovalStatus::Rejected->value]);

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
