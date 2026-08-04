<?php

namespace App\Livewire\Admin\Matches;

use App\Events\MatchCompleted;
use App\Events\MatchStarted;
use App\Livewire\Concerns\Notifies;
use App\Models\Match_;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class MatchesPage extends Component
{
    use Notifies;
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public int $perPage = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function resetPage()
    {
        $this->setPage(1);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function delete($id)
    {
        $match = Match_::findOrFail($id);
        $this->authorize('delete', $match);

        $match->delete();
        $this->notify('success', __('app.match_deleted'));
    }

    public function startMatch($id)
    {
        $match = Match_::findOrFail($id);
        $this->authorize('update', $match);

        $extra = $match->extra_data ?? [];
        $extra['started_at'] = now()->toIso8601String();

        $match->update([
            'status' => 'in_progress',
            'match_date' => now(),
            'score_team1' => 0,
            'score_team2' => 0,
            'extra_data' => $extra,
        ]);

        event(new MatchStarted($match->fresh()));

        $this->notify('success', __('app.match_started'));
    }

    public function endMatch($id)
    {
        $match = Match_::findOrFail($id);
        $this->authorize('update', $match);

        $match->update(['status' => 'completed']);

        event(new MatchCompleted($match->fresh()));

        $this->notify('success', __('app.match_ended'));
    }

    public function render()
    {
        $query = Match_::query()
            ->with(['competition', 'team1', 'team2'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->whereHas('competition', fn ($cq) => $cq->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('team1', fn ($tq) => $tq->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('team2', fn ($tq) => $tq->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        return view('livewire.admin.matches.matches-page', [
            'title' => __('app.matches'),
            'matches' => $query->latest('match_date')->paginate($this->perPage),
        ]);
    }
}
