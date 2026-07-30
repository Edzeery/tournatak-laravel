<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class EditTeamPage extends Component
{
    use WithFileUploads;

    public Team $team;

    public string $name = '';

    public ?int $captain_id = null;

    public ?string $logo = null;

    public $logoFile = null;

    public string $logoUrl = '';

    public string $logoSrc = 'none'; // 'none' | 'upload' | 'url'

    public bool $removeLogo = false;

    public int $points = 0;

    public function mount(Team $team)
    {
        $this->authorize('update', $team);

        $this->team = $team;
        $this->name = $team->name;
        $this->captain_id = $team->captain_id;
        $this->logo = $team->logo;
        $this->points = $team->points;

        if ($team->logo) {
            if (str_starts_with($team->logo, 'http')) {
                $this->logoSrc = 'url';
                $this->logoUrl = $team->logo;
            } else {
                $this->logoSrc = 'upload';
            }
        }
    }

    public function updatedRemoveLogo($value)
    {
        if ($value) {
            $this->logoSrc = 'none';
            $this->logoFile = null;
            $this->logoUrl = '';
        }
    }

    public function updatedLogoSrc($value)
    {
        if (in_array($value, ['upload', 'url'])) {
            $this->removeLogo = false;
        }
    }

    public function update()
    {
        $service = app(TeamService::class);
        $this->validate([
            'name' => 'required|string|max:255|unique:teams,name,'.$this->team->id,
            'captain_id' => 'nullable|exists:users,id',
            'logoFile' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:500',
            'logoUrl' => 'nullable|string|max:2048',
            'points' => 'integer|min:0',
        ]);

        $logo = $this->logo;

        if ($this->removeLogo) {
            $service->deleteLogoFile($this->logo);
            $logo = null;
        } elseif ($this->logoFile) {
            $service->deleteLogoFile($this->logo);
            $logo = $service->storeLogo($this->logoFile);
        } elseif ($this->logoSrc === 'url' && $this->logoUrl) {
            if (! str_starts_with($this->logo, 'http')) {
                $service->deleteLogoFile($this->logo);
            }
            $logo = $this->logoUrl;
        }

        $this->team->update([
            'name' => $this->name,
            'captain_id' => $this->captain_id,
            'logo' => $logo,
            'points' => $this->points,
        ]);

        $this->logo = $logo;
        $this->logoFile = null;

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
