<?php
namespace App\Livewire\Admin\Teams;

use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class TeamsPage extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetPage()
    {
        $this->setPage(1);
    }

    public function delete($id)
    {
        $team = Team::findOrFail($id);
        $this->authorize('delete', $team);

        $team->delete();
        session()->flash('success', __('app.team_deleted'));
    }

    public function render()
    {
        $query = Team::query()
            ->with('captain')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"));

        return view('livewire.admin.teams.teams-page', [
            'title' => __('app.manage_teams'),
            'teams' => $query->latest()->paginate($this->perPage),
        ]);
    }
}
