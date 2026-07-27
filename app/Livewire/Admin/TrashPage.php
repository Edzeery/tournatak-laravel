<?php

namespace App\Livewire\Admin;

use App\Models\Team;
use App\Models\Player;
use App\Models\Competition;
use App\Models\Match_;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class TrashPage extends Component
{
    use WithPagination;

    public string $filterType = 'all';
    public string $search = '';

    protected $queryString = ['filterType', 'search'];

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getRecords()
    {
        $query = match ($this->filterType) {
            'teams' => Team::onlyTrashed()->latest('deleted_at'),
            'players' => Player::onlyTrashed()->with('user', 'team')->latest('deleted_at'),
            'competitions' => Competition::onlyTrashed()->latest('deleted_at'),
            'matches' => Match_::onlyTrashed()->with('team1', 'team2')->latest('deleted_at'),
            'users' => User::onlyTrashed()->latest('deleted_at'),
            default => null,
        };

        if (! $query) {
            return collect();
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('id', '=', $this->search);
            });
        }

        return $query->paginate(15);
    }

    public function restore(string $type, int $id): void
    {
        $model = match ($type) {
            'teams' => Team::onlyTrashed()->findOrFail($id),
            'players' => Player::onlyTrashed()->findOrFail($id),
            'competitions' => Competition::onlyTrashed()->findOrFail($id),
            'matches' => Match_::onlyTrashed()->findOrFail($id),
            'users' => User::onlyTrashed()->findOrFail($id),
        };

        $model->restore();
        session()->flash('success', __('app.record_restored'));
    }

    public function forceDelete(string $type, int $id): void
    {
        $model = match ($type) {
            'teams' => Team::onlyTrashed()->findOrFail($id),
            'players' => Player::onlyTrashed()->findOrFail($id),
            'competitions' => Competition::onlyTrashed()->findOrFail($id),
            'matches' => Match_::onlyTrashed()->findOrFail($id),
            'users' => User::onlyTrashed()->findOrFail($id),
        };

        $model->forceDelete();
        session()->flash('success', __('app.record_deleted_permanently'));
    }

    public function render()
    {
        return view('livewire.admin.trash-page', [
            'records' => $this->getRecords(),
            'title' => __('app.trash'),
        ]);
    }
}
