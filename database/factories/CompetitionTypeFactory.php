<?php

namespace Database\Factories;

use App\Models\CompetitionType;
use App\Models\CompetitionSubtype;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompetitionType>
 */
class CompetitionTypeFactory extends Factory
{
    protected $model = CompetitionType::class;

    public function definition(): array
    {
        return [
            'subtype_id' => CompetitionSubtype::factory(),
            'name' => fake()->unique()->word(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'icon' => null,
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }
}
