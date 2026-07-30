<?php

namespace App\Livewire\Admin\Registrations;

use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\Team;
use App\Services\RegistrationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateTeamRegistrationPage extends Component
{
    public ?int $competition_id = null;

    public ?int $team_id = null;

    public string $searchTeam = '';

    public function store(RegistrationService $service)
    {
        $this->validate([
            'competition_id' => 'required|exists:competitions,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $result = $service->registerTeam($this->competition_id, $this->team_id);

        if (! $result['success']) {
            session()->flash('error', $result['message']);

            return;
        }

        session()->flash('success', __('app.team_registration_created'));

        return redirect()->route('admin.registrations.index');
    }

    public function render()
    {
        $teamTypeIds = CompetitionType::whereIn('participant_type', ['team', 'both'])->pluck('id');

        $competitions = Competition::whereIn('type_id', $teamTypeIds)
            ->where('approval_status', 'approved')
            ->latest()
            ->get();

        $teams = Team::query()
            ->when($this->searchTeam, fn ($q) => $q->where('name', 'like', "%{$this->searchTeam}%"))
            ->orderBy('name')
            ->limit(20)
            ->get();

        return view('livewire.admin.registrations.create-team-registration-page', [
            'title' => __('app.page_title_add_team_registration'),
            'competitions' => $competitions,
            'teams' => $teams,
        ]);
    }
}
