<?php

namespace App\Livewire\Admin\Competitions;

use App\Models\CompetitionSubtype;
use App\Models\CompetitionType;
use App\Services\CompetitionService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateCompetitionPage extends Component
{
    public string $name = '';

    public ?int $type_id = null;

    public ?int $subtype_id = null;

    public ?string $location = null;

    public ?string $start_date = null;

    public ?string $end_date = null;

    public ?string $description = null;

    public function store()
    {
        $service = app(CompetitionService::class);
        $this->validate($service->getValidationRules());

        $service->create([
            'name' => $this->name,
            'type_id' => $this->type_id,
            'subtype_id' => $this->subtype_id,
            'location' => $this->location,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'description' => $this->description,
        ]);

        session()->flash('success', __('app.competition_created'));

        return redirect()->route('admin.competitions.index');
    }

    public function render()
    {
        return view('livewire.admin.competitions.create-competition-page', [
            'title' => __('app.page_title_add_competition'),
            'types' => CompetitionType::where('is_active', true)->get(),
            'subtypes' => CompetitionSubtype::all(),
        ]);
    }
}
