<?php
namespace App\Livewire\Admin\Teams;

use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class EditTeamPage extends Component
{
    public Team $team;
    public string $name = '';
    public ?int $captain_id = null;
    public ?string $logo = null;
    public int $points = 0;

    public function mount(Team $team)
    {
        $this->authorize('update', $team);

        $this->team = $team;
        $this->name = $team->name;
        $this->captain_id = $team->captain_id;
        $this->logo = $team->logo;
        $this->points = $team->points;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $this->team->id,
            'captain_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|string|max:255',
            'points' => 'integer|min:0',
        ]);

        $this->team->update([
            'name' => $this->name,
            'captain_id' => $this->captain_id,
            'logo' => $this->logo,
            'points' => $this->points,
        ]);

        session()->flash('success', __('app.team_updated'));
        return redirect()->route('admin.teams.index');
    }

    public function render()
    {
        return view('livewire.admin.teams.edit-team-page', [
            'title' => __('app.edit_team'),
            'team' => $this->team,
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
