<?php

namespace Database\Factories;

use App\Models\CompetitionDomain;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompetitionDomain>
 */
class CompetitionDomainFactory extends Factory
{
    protected $model = CompetitionDomain::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'name_en' => fake()->unique()->words(2, true),
            'name_fr' => fake()->unique()->words(2, true),
            'name_es' => fake()->unique()->words(2, true),
            'slug' => fake()->unique()->slug(),
            'icon' => 'bi-trophy',
            'description' => fake()->sentence(),
            'evaluation_basis' => 'match',
            'participant_basis' => 'both',
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }

    public function submission(): static
    {
        return $this->state(['evaluation_basis' => 'submission']);
    }

    public function individual(): static
    {
        return $this->state(['participant_basis' => 'individual']);
    }

    public function sports(): static
    {
        return $this->state([
            'name' => 'الرياضات',
            'name_en' => 'Sports',
            'name_fr' => 'Sports',
            'name_es' => 'Deportes',
            'slug' => CompetitionDomain::SLUG_SPORTS,
            'icon' => 'bi-trophy',
            'evaluation_basis' => 'match',
            'participant_basis' => 'both',
            'sort_order' => 1,
        ]);
    }
}
