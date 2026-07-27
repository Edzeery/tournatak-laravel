<?php
namespace App\Livewire\Admin\Teams;

use App\Models\Team;
use App\Models\User;
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
        $this->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'captain_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|string|max:255',
            'points' => 'integer|min:0',
        ]);

        Team::create([
            'name' => $this->name,
            'captain_id' => $this->captain_id,
            'logo' => $this->logo,
            'points' => $this->points,
        ]);

        session()->flash('success', 'تم إنشاء الفريق بنجاح');
        return redirect()->route('admin.teams.index');
    }

    public function render()
    {
        return view('livewire.admin.teams.create-team-page', [
            'title' => 'إضافة فريق',
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
