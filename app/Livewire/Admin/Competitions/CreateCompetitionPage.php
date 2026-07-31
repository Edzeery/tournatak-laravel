<?php

namespace App\Livewire\Admin\Competitions;

use App\Models\CompetitionDomain;
use App\Models\CompetitionSubtype;
use App\Models\CompetitionType;
use App\Services\CompetitionService;
use App\Services\CompetitionSetupService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateCompetitionPage extends Component
{
    const STEP_DOMAIN = 'domain';

    const STEP_BASICS = 'basics';

    const STEP_ROUNDS = 'rounds';

    const STEP_REVIEW = 'review';

    public string $step = self::STEP_DOMAIN;

    public ?int $domain_id = null;

    public string $name = '';

    public ?int $type_id = null;

    public ?int $subtype_id = null;

    public ?string $location = null;

    public ?string $start_date = null;

    public ?string $end_date = null;

    public ?string $description = null;

    public ?int $rounds_count = null;

    public ?string $judging_criteria = null;

    public function domains()
    {
        return CompetitionDomain::where('is_active', true)->orderBy('sort_order')->get();
    }

    public function types()
    {
        return CompetitionType::where('is_active', true)->get();
    }

    public function subtypes()
    {
        return CompetitionSubtype::all();
    }

    public function selectedDomain(): ?CompetitionDomain
    {
        return $this->domain_id ? CompetitionDomain::find($this->domain_id) : null;
    }

    public function steps(): array
    {
        $domain = $this->selectedDomain();

        if (! $domain) {
            return [self::STEP_DOMAIN];
        }

        if ($domain->usesSubmissionEvaluation()) {
            return app(CompetitionSetupService::class)->stepsFor($domain);
        }

        return [self::STEP_DOMAIN, self::STEP_BASICS, self::STEP_REVIEW];
    }

    public function stepIndex(): int
    {
        $index = array_search($this->step, $this->steps(), true);

        return $index === false ? 0 : $index;
    }

    public function selectDomain(int $domainId): void
    {
        CompetitionDomain::where('is_active', true)->findOrFail($domainId);

        $this->domain_id = $domainId;
        $this->step = self::STEP_BASICS;
    }

    public function goToStep(string $step): void
    {
        if (! in_array($step, $this->steps(), true)) {
            return;
        }

        if ($step !== self::STEP_DOMAIN && ! $this->selectedDomain()) {
            return;
        }

        $this->step = $step;
    }

    public function previousStep(): void
    {
        $this->step = $this->steps()[max(0, $this->stepIndex() - 1)];
    }

    public function nextStep(): void
    {
        $this->validateStep();

        $steps = $this->steps();
        $this->step = $steps[min(count($steps) - 1, $this->stepIndex() + 1)];
    }

    protected function validateStep(): void
    {
        $domain = $this->selectedDomain();

        if (! $domain) {
            return;
        }

        $rules = [];

        if ($this->step === self::STEP_BASICS) {
            if ($domain->usesSubmissionEvaluation()) {
                $rules = $this->rulesFor(CompetitionSetupService::STEP_BASICS, $domain);
            } else {
                $rules = app(CompetitionService::class)->getValidationRules();
            }
        } elseif ($this->step === self::STEP_ROUNDS) {
            $rules = $this->rulesFor(CompetitionSetupService::STEP_ROUNDS, $domain);
        }

        if ($rules !== []) {
            $this->validate($rules);
        }
    }

    protected function rulesFor(string $step, CompetitionDomain $domain): array
    {
        $setup = app(CompetitionSetupService::class);
        $all = $setup->validationFor($domain);
        $rules = [];

        foreach ($setup->fieldsFor($step, $domain) as $field) {
            $rules[$field['name']] = $all[$field['name']];
        }

        return $rules;
    }

    public function basicsFields(): array
    {
        $domain = $this->selectedDomain();

        if (! $domain) {
            return [];
        }

        $setup = app(CompetitionSetupService::class);
        $fields = $setup->fieldsFor(CompetitionSetupService::STEP_BASICS, $domain);

        if ($domain->usesSubmissionEvaluation()) {
            return $fields;
        }

        return [
            $fields[0],
            [
                'name' => 'type_id',
                'label' => __('app.type'),
                'type' => CompetitionSetupService::FIELD_SELECT,
                'options' => $this->types()->pluck('name', 'id')->all(),
                'required' => true,
            ],
            [
                'name' => 'subtype_id',
                'label' => __('app.subtype'),
                'type' => CompetitionSetupService::FIELD_SELECT,
                'options' => $this->subtypes()->pluck('name', 'id')->all(),
                'required' => true,
            ],
            $fields[2],
            $fields[3],
            $fields[4],
            $fields[1],
        ];
    }

    public function roundsFields(): array
    {
        $domain = $this->selectedDomain();

        if (! $domain) {
            return [];
        }

        return app(CompetitionSetupService::class)->fieldsFor(CompetitionSetupService::STEP_ROUNDS, $domain);
    }

    public function stepFields(): array
    {
        if ($this->step === self::STEP_ROUNDS) {
            return $this->roundsFields();
        }

        return $this->basicsFields();
    }

    public function reviewItems(): array
    {
        $domain = $this->selectedDomain();
        $items = [
            ['label' => __('app.domain'), 'value' => $domain?->localizedName() ?? '-'],
            ['label' => __('app.name'), 'value' => $this->name ?: '-'],
        ];

        if ($domain?->usesSubmissionEvaluation()) {
            $items[] = ['label' => __('app.rounds_count'), 'value' => $this->rounds_count !== null ? (string) $this->rounds_count : '-'];
            $items[] = ['label' => __('app.judging_criteria'), 'value' => $this->judging_criteria ?: '-'];
        } else {
            $items[] = ['label' => __('app.type'), 'value' => $this->types()->firstWhere('id', $this->type_id)?->name ?? '-'];
            $items[] = ['label' => __('app.subtype'), 'value' => $this->subtypes()->firstWhere('id', $this->subtype_id)?->name ?? '-'];
        }

        $items[] = ['label' => __('app.location'), 'value' => $this->location ?: '-'];
        $items[] = ['label' => __('app.start_date'), 'value' => $this->start_date ?: '-'];
        $items[] = ['label' => __('app.end_date'), 'value' => $this->end_date ?: '-'];
        $items[] = ['label' => __('app.description'), 'value' => $this->description ? Str::limit($this->description, 120) : '-'];

        return $items;
    }

    public function store()
    {
        $domain = $this->selectedDomain();

        if (! $domain) {
            $this->step = self::STEP_DOMAIN;

            return;
        }

        $this->step = self::STEP_BASICS;
        $this->validateStep();

        if ($domain->usesSubmissionEvaluation()) {
            $this->step = self::STEP_ROUNDS;
            $this->validateStep();
        }

        $this->step = self::STEP_REVIEW;

        $service = app(CompetitionService::class);
        $setup = app(CompetitionSetupService::class);

        if ($domain->usesSubmissionEvaluation()) {
            $provision = $setup->provisionTypeFor($domain);
            $data = [
                'name' => $this->name,
                'domain_id' => $domain->id,
                'type_id' => $provision['type_id'],
                'subtype_id' => $provision['subtype_id'],
                'location' => $this->location,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'description' => $this->description,
                'format_config' => array_filter([
                    'rounds_count' => $this->rounds_count,
                    'judging_criteria' => $this->judging_criteria,
                ], fn ($value) => $value !== null && $value !== ''),
            ];
        } else {
            $data = [
                'name' => $this->name,
                'domain_id' => $domain->id,
                'type_id' => $this->type_id,
                'subtype_id' => $this->subtype_id,
                'location' => $this->location,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'description' => $this->description,
            ];
        }

        $service->create($data);

        session()->flash('success', __('app.competition_created'));

        return redirect()->route('admin.competitions.index');
    }

    public function render()
    {
        return view('livewire.admin.competitions.create-competition-page', [
            'title' => __('app.page_title_add_competition'),
        ]);
    }
}
