<?php

namespace App\Livewire\Public;

use App\Models\Competition;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CompetitionsPage extends Component
{
    public function render()
    {
        return view('livewire.public.competitions-page', [
            'title' => __('app.page_title_competitions'),
            'competitions' => Competition::where('approval_status', 'approved')
                ->with(['type', 'subtype', 'organizer'])
                ->latest()
                ->paginate(12),
        ]);
    }
}
