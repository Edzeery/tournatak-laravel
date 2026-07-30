<?php

namespace App\Livewire\User;

use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\Registration;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RegistrationsPage extends Component
{
    public string $participantType = 'individual';

    public ?int $competition_id = null;

    public ?int $team_id = null;

    public function register()
    {
        $this->validate([
            'participantType' => 'required|in:individual,team',
            'competition_id' => 'required|exists:competitions,id',
            'team_id' => 'required_if:participantType,team|exists:teams,id|nullable',
        ]);

        if ($this->participantType === 'individual') {
            $existing = Registration::where('competition_id', $this->competition_id)
                ->where('participant_type', Registration::PARTICIPANT_INDIVIDUAL)
                ->where('user_id', auth()->id())
                ->first();

            if ($existing) {
                session()->flash('error', __('app.registration_already_exists'));

                return;
            }

            Registration::create([
                'competition_id' => $this->competition_id,
                'participant_type' => Registration::PARTICIPANT_INDIVIDUAL,
                'user_id' => auth()->id(),
                'status' => Registration::STATUS_PENDING,
            ]);

            session()->flash('success', __('app.registration_submitted'));
        } else {
            $existing = Registration::where('competition_id', $this->competition_id)
                ->where('participant_type', Registration::PARTICIPANT_TEAM)
                ->where('team_id', $this->team_id)
                ->first();

            if ($existing) {
                session()->flash('error', __('app.registration_team_already_exists'));

                return;
            }

            Registration::create([
                'competition_id' => $this->competition_id,
                'participant_type' => Registration::PARTICIPANT_TEAM,
                'team_id' => $this->team_id,
                'status' => Registration::STATUS_PENDING,
            ]);

            session()->flash('success', __('app.registration_submitted'));
        }

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
