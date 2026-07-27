<?php
namespace App\Livewire\Admin\Teams;

use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateTeamPage extends Component
{
    public string $name = '';
    public ?int $captain_id = null;
    public ?string $logo = null;
    public int $points = 0;

    public function store()
    {
        $service = app(TeamService::class);
        $this->validate($service->getValidationRules());

        $service->create([
            'name' => $this->name,
            'captain_id' => $this->captain_id,
            'logo' => $this->logo,
            'points' => $this->points,
        ]);

        session()->flash('success', __('app.team_created'));
        return redirect()->route('admin.teams.index');
    }

    public function render()
    {
        return view('livewire.admin.teams.create-team-page', [
            'title' => __('app.add_team'),
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
