<?php

namespace App\Livewire\Admin\Competitions;

use App\Models\Competition;
use App\Models\CompetitionSubtype;
use App\Models\CompetitionType;
use App\Services\CompetitionService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class EditCompetitionPage extends Component
{
    public Competition $competition;

    public string $name = '';

    public ?int $type_id = null;

    public ?int $subtype_id = null;

    public ?string $location = null;

    public ?string $start_date = null;

    public ?string $end_date = null;

    public ?string $description = null;

    public string $status = 'draft';

    public string $approval_status = 'pending';

    public function mount(Competition $competition)
    {
        $this->authorize('update', $competition);

        $this->competition = $competition;
        $this->name = $competition->name;
        $this->type_id = $competition->type_id;
        $this->subtype_id = $competition->subtype_id;
        $this->location = $competition->location;
        $this->start_date = $competition->start_date?->format('Y-m-d\TH:i');
        $this->end_date = $competition->end_date?->format('Y-m-d\TH:i');
        $this->description = $competition->description;
        $this->status = $competition->status;
        $this->approval_status = $competition->approval_status;
    }

    public function update(CompetitionService $service)
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:competition_types,id',
            'subtype_id' => 'required|exists:competition_subtypes,id',
        ]);

        $service->update($this->competition, [
            'name' => $this->name,
            'type_id' => $this->type_id,
            'subtype_id' => $this->subtype_id,
            'location' => $this->location,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'description' => $this->description,
            'status' => $this->status,
            'approval_status' => $this->approval_status,
        ]);

        session()->flash('success', __('app.competition_updated'));

        return redirect()->route('admin.competitions.index');
    }

    public function render()
    {
        return view('livewire.admin.competitions.edit-competition-page', [
            'title' => __('app.page_title_edit_competition'),
            'competition' => $this->competition,
            'types' => CompetitionType::where('is_active', true)->get(),
            'subtypes' => CompetitionSubtype::all(),
        ]);
    }
}
