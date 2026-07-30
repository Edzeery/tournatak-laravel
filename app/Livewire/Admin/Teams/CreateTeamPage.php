<?php

namespace App\Livewire\Admin\Teams;

use App\Models\User;
use App\Services\TeamService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class CreateTeamPage extends Component
{
    use WithFileUploads;

    public string $name = '';

    public ?int $captain_id = null;

    public $logoFile = null;

    public string $logoUrl = '';

    public string $logoSrc = 'upload'; // 'upload' | 'url'

    public int $points = 0;

    public function store()
    {
        $service = app(TeamService::class);
        $this->validate($service->getValidationRules());

        $logo = null;

        if ($this->logoSrc === 'upload' && $this->logoFile) {
            $logo = $service->storeLogo($this->logoFile);
        } elseif ($this->logoSrc === 'url' && $this->logoUrl) {
            $logo = $this->logoUrl;
        }

        $service->create([
            'name' => $this->name,
            'captain_id' => $this->captain_id,
            'logo' => $logo,
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
