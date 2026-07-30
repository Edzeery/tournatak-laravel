<?php

namespace App\Livewire\Admin\Competitions;

use App\Models\Competition;
use App\Models\CompetitionSubtype;
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

        $subtype = CompetitionSubtype::firstOrCreate(
            ['name' => 'General'],
            ['en_name' => 'General']
        );

        $type = CompetitionType::firstOrCreate(
            ['slug' => 'community-tournament'],
            [
                'name' => 'Community Tournament',
                'subtype_id' => $subtype->id,
                'participant_type' => 'both',
                'sort_order' => 999,
                'is_active' => true,
            ]
        );

        $service->create([
            'name' => $this->name,
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
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
