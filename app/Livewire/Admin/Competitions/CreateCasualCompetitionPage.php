<?php

namespace App\Livewire\Admin\Competitions;

use App\Models\Competition;
use App\Models\CompetitionType;
use App\Services\CompetitionService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateCasualCompetitionPage extends Component
{
    public string $name = '';

    public string $format = 'knockout';

    public ?string $start_date = null;

    public ?string $location = null;

    public function store()
    {
        $service = app(CompetitionService::class);
        $this->validate($service->getCasualValidationRules());

        $type = CompetitionType::firstOrCreate([
            'name' => 'بطولة مجتمعية',
            'name_en' => 'Community Tournament',
            'is_active' => true,
        ]);

        $service->create([
            'name' => $this->name,
            'type_id' => $type->id,
            'subtype_id' => null,
            'format' => $this->format,
            'start_date' => $this->start_date,
            'location' => $this->location,
            'competition_profile' => Competition::PROFILE_CASUAL,
        ]);

        session()->flash('success', __('app.casual_competition_created'));

        return redirect()->route('admin.competitions.index');
    }

    public function render()
    {
        return view('livewire.admin.competitions.create-casual-competition-page', [
            'title' => __('app.page_title_create_casual_competition'),
            'formats' => [
                'knockout' => __('app.format_knockout'),
                'groups' => __('app.format_groups'),
                'league' => __('app.format_league'),
            ],
        ]);
    }
}
