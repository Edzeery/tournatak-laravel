<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionDomain;
use App\Models\CompetitionSubtype;
use App\Models\CompetitionType;
use App\Models\Sport;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompetitionSetupService
{
    const STEP_DOMAIN = 'domain';

    const STEP_BASICS = 'basics';

    const STEP_FORMAT = 'format';

    const STEP_ROUNDS = 'rounds';

    const STEP_REVIEW = 'review';

    const FIELD_TEXT = 'text';

    const FIELD_TEXTAREA = 'textarea';

    const FIELD_SELECT = 'select';

    const FIELD_DATE = 'date';

    const FIELD_DATE_TIME = 'datetime';

    const FORMATS = [
        Competition::FORMAT_KNOCKOUT,
        Competition::FORMAT_GROUPS,
        Competition::FORMAT_LEAGUE,
        Competition::FORMAT_LEAGUE_KNOCKOUT,
        Competition::FORMAT_DOUBLE_ELIMINATION,
        Competition::FORMAT_SWISS,
        Competition::FORMAT_HOME_AWAY,
    ];

    /**
     * Ordered wizard steps per domain.
     *
     * @return array<int, string>
     */
    public function stepsFor(CompetitionDomain $domain): array
    {
        return $domain->usesSubmissionEvaluation()
            ? [self::STEP_DOMAIN, self::STEP_BASICS, self::STEP_ROUNDS, self::STEP_REVIEW]
            : [self::STEP_DOMAIN, self::STEP_BASICS, self::STEP_FORMAT, self::STEP_REVIEW];
    }

    /**
     * Field descriptors for a wizard step.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fieldsFor(string $step, CompetitionDomain $domain): array
    {
        return match ($step) {
            self::STEP_DOMAIN => [
                [
                    'name' => 'domain_id',
                    'label' => __('app.domain'),
                    'type' => self::FIELD_SELECT,
                    'options' => [$domain->id => $domain->localizedName()],
                    'validation' => ['required', 'exists:competition_domains,id'],
                    'required' => true,
                ],
            ],
            self::STEP_BASICS => [
                [
                    'name' => 'name',
                    'label' => __('app.name'),
                    'type' => self::FIELD_TEXT,
                    'options' => [],
                    'validation' => ['required', 'string', 'max:255'],
                    'required' => true,
                ],
                [
                    'name' => 'description',
                    'label' => __('app.description'),
                    'type' => self::FIELD_TEXTAREA,
                    'options' => [],
                    'validation' => ['nullable', 'string'],
                    'required' => false,
                ],
                [
                    'name' => 'location',
                    'label' => __('app.location'),
                    'type' => self::FIELD_TEXT,
                    'options' => [],
                    'validation' => ['nullable', 'string', 'max:255'],
                    'required' => false,
                ],
                [
                    'name' => 'start_date',
                    'label' => __('app.start_date'),
                    'type' => self::FIELD_DATE_TIME,
                    'options' => [],
                    'validation' => ['nullable', 'date'],
                    'required' => false,
                ],
                [
                    'name' => 'end_date',
                    'label' => __('app.end_date'),
                    'type' => self::FIELD_DATE_TIME,
                    'options' => [],
                    'validation' => ['nullable', 'date', 'after:start_date'],
                    'required' => false,
                ],
            ],
            self::STEP_FORMAT => [
                [
                    'name' => 'sport_id',
                    'label' => __('app.sport'),
                    'type' => self::FIELD_SELECT,
                    'options' => Sport::pluck('name', 'id')->all(),
                    'validation' => ['required', 'exists:sports,id'],
                    'required' => true,
                ],
                [
                    'name' => 'format',
                    'label' => __('app.format'),
                    'type' => self::FIELD_SELECT,
                    'options' => $this->formatOptions(),
                    'validation' => ['required', Rule::in(self::FORMATS)],
                    'required' => true,
                ],
            ],
            self::STEP_ROUNDS => [
                [
                    'name' => 'rounds_count',
                    'label' => __('app.rounds_count'),
                    'type' => self::FIELD_TEXT,
                    'options' => [],
                    'validation' => ['nullable', 'integer', 'min:1', 'max:16'],
                    'required' => false,
                ],
                [
                    'name' => 'judging_criteria',
                    'label' => __('app.judging_criteria'),
                    'type' => self::FIELD_TEXTAREA,
                    'options' => [],
                    'validation' => ['nullable', 'string'],
                    'required' => false,
                ],
            ],
            self::STEP_REVIEW => [],
            default => [],
        };
    }

    /**
     * Laravel validation rules per domain step.
     *
     * @return array<string, mixed>
     */
    public function validationFor(CompetitionDomain $domain): array
    {
        $rules = [];

        foreach ($this->stepsFor($domain) as $step) {
            foreach ($this->fieldsFor($step, $domain) as $field) {
                $rules[$field['name']] = $field['validation'];
            }
        }

        return $rules;
    }

    /**
     * Provision (or reuse) the per-domain competition type and subtype.
     *
     * @param  array<string, mixed>  $data
     * @return array{type_id: int, subtype_id: int}
     */
    public function provisionTypeFor(CompetitionDomain $domain, array $data = []): array
    {
        $subtype = CompetitionSubtype::firstOrCreate(
            ['name' => $domain->localizedName('en')],
            ['en_name' => $domain->localizedName('en')]
        );

        $type = CompetitionType::firstOrCreate(
            ['slug' => 'general-'.Str::slug($domain->slug)],
            [
                'name' => $data['type_name'] ?? __('app.general_competition_type', ['domain' => $domain->localizedName()]),
                'subtype_id' => $subtype->id,
                'participant_type' => $data['participant_type'] ?? ($domain->participant_basis ?: CompetitionDomain::PARTICIPANT_BOTH),
                'sort_order' => 999,
                'is_active' => true,
            ]
        );

        return ['type_id' => $type->id, 'subtype_id' => $subtype->id];
    }

    /**
     * @return array<string, string>
     */
    protected function formatOptions(): array
    {
        $options = [];

        foreach (self::FORMATS as $format) {
            $options[$format] = Competition::FORMATS[$format] ?? $format;
        }

        return $options;
    }
}
