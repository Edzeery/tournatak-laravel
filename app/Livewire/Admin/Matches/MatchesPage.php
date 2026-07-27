<?php
namespace App\Livewire\Admin\Matches;

use App\Models\Match_;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class MatchesPage extends Component
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
        $match = Match_::findOrFail($id);
        $match->delete();
        session()->flash('success', __('app.match_deleted'));
    }

    public function render()
    {
        $query = Match_::query()
            ->with(['competition', 'team1', 'team2'])
            ->when($this->search, fn($q) => $q->whereHas('competition', fn($cq) => $cq->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('team1', fn($tq) => $tq->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('team2', fn($tq) => $tq->where('name', 'like', "%{$this->search}%")));

        return view('livewire.admin.matches.matches-page', [
            'title' => __('app.matches'),
            'matches' => $query->latest('match_date')->paginate($this->perPage),
        ]);
    }
}
