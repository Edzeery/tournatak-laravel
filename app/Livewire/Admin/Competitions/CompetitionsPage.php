<?php

namespace App\Livewire\Admin\Competitions;

use App\Models\Competition;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class CompetitionsPage extends Component
{
    use WithPagination;

    public function approve($id)
    {
        $competition = Competition::findOrFail($id);
        $this->authorize('update', $competition);

        $competition->update(['approval_status' => 'approved']);
        session()->flash('success', __('app.competition_approved'));
    }

    public function reject($id)
    {
        $competition = Competition::findOrFail($id);
        $this->authorize('update', $competition);

        $competition->update(['approval_status' => 'rejected']);
        session()->flash('error', __('app.competition_rejected'));
    }

    public function render()
    {
        return view('livewire.admin.competitions.competitions-page', [
            'title' => __('app.manage_competitions'),
            'competitions' => Competition::with('organizer', 'type', 'domain')->latest()->paginate(10),
        ]);
    }
}
