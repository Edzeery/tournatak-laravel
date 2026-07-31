<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\CompetitionDomain;
use App\Models\CompetitionSubtype;
use App\Models\CompetitionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        return [
            'type_id' => CompetitionType::factory(),
            'subtype_id' => CompetitionSubtype::factory(),
            'organizer_id' => User::factory(),
            'domain_id' => CompetitionDomain::factory(),
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'start_date' => fake()->dateTimeBetween('+1 month', '+3 months'),
            'end_date' => fake()->dateTimeBetween('+3 months', '+6 months'),
            'location' => fake()->city(),
            'approval_status' => 'pending',
            'status' => 'draft',
            'format' => fake()->randomElement([
                Competition::FORMAT_LEAGUE,
                Competition::FORMAT_KNOCKOUT,
                Competition::FORMAT_GROUPS,
                Competition::FORMAT_SWISS,
            ]),
            'format_config' => [],
        ];
    }

    public function sports(): static
    {
        return $this->state(fn () => [
            'domain_id' => CompetitionDomain::query()->where('slug', CompetitionDomain::SLUG_SPORTS)->value('id'),
        ]);
    }

    public function submission(): static
    {
        return $this->state(fn () => [
            'domain_id' => CompetitionDomain::query()
                ->where('evaluation_basis', 'submission')
                ->value('id'),
        ]);
    }
}
