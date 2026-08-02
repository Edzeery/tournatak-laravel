<?php

namespace App\Livewire\User;

use App\Livewire\Concerns\Notifies;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\Registration;
use App\Services\RegistrationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RegistrationsPage extends Component
{
    use Notifies;

    public string $participantType = 'individual';

    public ?int $competition_id = null;

    public ?int $team_id = null;

    public function register(RegistrationService $service)
    {
        $this->validate([
            'participantType' => 'required|in:individual,team',
            'competition_id' => 'required|exists:competitions,id',
            'team_id' => 'required_if:participantType,team|exists:teams,id|nullable',
        ]);

        $status = Registration::STATUS_PENDING;

        if ($this->participantType === 'individual') {
            $result = $service->registerIndividual($this->competition_id, auth()->id(), $status);
        } else {
            $result = $service->registerTeam($this->competition_id, $this->team_id, $status);
        }

        if (! $result['success']) {
            $this->notify('error', $result['message']);

            return;
        }

        $this->notify('success', __('app.registration_submitted'));
        $this->reset(['competition_id', 'team_id']);
    }

    public function render()
    {
        $user = auth()->user();

        $individualTypeIds = CompetitionType::whereIn('participant_type', ['individual', 'both'])->pluck('id');
        $teamTypeIds = CompetitionType::whereIn('participant_type', ['team', 'both'])->pluck('id');

        $individualRegistrations = Registration::where('user_id', $user->id)
            ->where('participant_type', Registration::PARTICIPANT_INDIVIDUAL)
            ->with(['competition.type', 'competition.subtype'])
            ->latest()
            ->get();

        $userTeamIds = $user->teams()->pluck('id');
        $teamRegistrations = Registration::whereIn('team_id', $userTeamIds)
            ->where('participant_type', Registration::PARTICIPANT_TEAM)
            ->with(['competition.type', 'competition.subtype', 'team'])
            ->latest()
            ->get();

        $registeredCompetitionIds = $individualRegistrations->pluck('competition_id')
            ->merge($teamRegistrations->pluck('competition_id'))
            ->unique();

        $availableIndividualCompetitions = Competition::whereIn('type_id', $individualTypeIds)
            ->where('approval_status', 'approved')
            ->whereNotIn('id', $registeredCompetitionIds)
            ->latest()
            ->get();

        $availableTeamCompetitions = Competition::whereIn('type_id', $teamTypeIds)
            ->where('approval_status', 'approved')
            ->whereNotIn('id', $registeredCompetitionIds)
            ->latest()
            ->get();

        $userTeams = $user->teams;

        return view('livewire.user.registrations-page', [
            'title' => __('app.my_registrations'),
            'individualRegistrations' => $individualRegistrations,
            'teamRegistrations' => $teamRegistrations,
            'availableIndividualCompetitions' => $availableIndividualCompetitions,
            'availableTeamCompetitions' => $availableTeamCompetitions,
            'userTeams' => $userTeams,
        ]);
    }
}
