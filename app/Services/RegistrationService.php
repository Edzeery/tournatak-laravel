<?php

namespace App\Services;

use App\Enums\ParticipantType;
use App\Enums\RegistrationStatus;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RegistrationService
{
    public function registerIndividual(int $competitionId, int $userId, string $status = RegistrationStatus::Approved->value): array
    {
        $competition = Competition::findOrFail($competitionId);

        $existing = $this->findExistingIndividual($competitionId, $userId);
        if ($existing) {
            return ['success' => false, 'message' => __('app.registration_already_exists')];
        }

        $registration = Registration::create([
            'competition_id' => $competitionId,
            'participant_type' => ParticipantType::Individual->value,
            'user_id' => $userId,
            'status' => $status,
        ]);

        return ['success' => true, 'registration' => $registration];
    }

    public function registerTeam(int $competitionId, int $teamId, string $status = RegistrationStatus::Approved->value): array
    {
        $competition = Competition::findOrFail($competitionId);

        $existing = $this->findExistingTeam($competitionId, $teamId);
        if ($existing) {
            return ['success' => false, 'message' => __('app.registration_team_already_exists')];
        }

        $registration = Registration::create([
            'competition_id' => $competitionId,
            'participant_type' => ParticipantType::Team->value,
            'team_id' => $teamId,
            'status' => $status,
        ]);

        return ['success' => true, 'registration' => $registration];
    }

    public function findExistingIndividual(int $competitionId, int $userId): ?Registration
    {
        return Registration::where('competition_id', $competitionId)
            ->where('participant_type', ParticipantType::Individual->value)
            ->where('user_id', $userId)
            ->first();
    }

    public function findExistingTeam(int $competitionId, int $teamId): ?Registration
    {
        return Registration::where('competition_id', $competitionId)
            ->where('participant_type', ParticipantType::Team->value)
            ->where('team_id', $teamId)
            ->first();
    }

    public function getUserRegistrations(User $user): Collection
    {
        $individualRegistrations = Registration::where('user_id', $user->id)
            ->where('participant_type', ParticipantType::Individual->value)
            ->with(['competition.type', 'competition.subtype'])
            ->latest()
            ->get();

        $userTeamIds = $user->teams()->pluck('id');
        $teamRegistrations = Registration::whereIn('team_id', $userTeamIds)
            ->where('participant_type', ParticipantType::Team->value)
            ->with(['competition.type', 'competition.subtype', 'team'])
            ->latest()
            ->get();

        return $individualRegistrations->merge($teamRegistrations);
    }

    public function getAvailableCompetitions(User $user, string $participantType): Collection
    {
        $typeIds = $participantType === ParticipantType::Individual->value
            ? CompetitionType::whereIn('participant_type', [ParticipantType::Individual->value, ParticipantType::Both->value])->pluck('id')
            : CompetitionType::whereIn('participant_type', [ParticipantType::Team->value, ParticipantType::Both->value])->pluck('id');

        $registeredIds = Registration::where(function ($q) use ($user, $participantType) {
            if ($participantType === ParticipantType::Individual->value) {
                $q->where('user_id', $user->id);
            } else {
                $q->whereIn('team_id', $user->teams()->pluck('id'));
            }
        })->pluck('competition_id');

        return Competition::whereIn('type_id', $typeIds)
            ->where('approval_status', 'approved')
            ->whereNotIn('id', $registeredIds)
            ->latest()
            ->get();
    }
}
