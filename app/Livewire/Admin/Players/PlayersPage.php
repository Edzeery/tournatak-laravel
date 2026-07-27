<?php
namespace App\Livewire\Admin\Players;

use App\Models\Player;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class PlayersPage extends Component
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
        $player = Player::findOrFail($id);
        $player->delete();
        session()->flash('success', __('app.player_deleted'));
    }

    public function render()
    {
        $query = Player::query()
            ->with(['user', 'team', 'position'])
            ->when($this->search, fn($q) => $q->whereHas('user', fn($uq) => $uq->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('team', fn($tq) => $tq->where('name', 'like', "%{$this->search}%")));

        return view('livewire.admin.players.players-page', [
            'title' => __('app.page_title_manage_players'),
            'players' => $query->latest()->paginate($this->perPage),
        ]);
    }
}
