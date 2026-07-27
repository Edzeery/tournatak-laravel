<?php

namespace Database\Factories;

use App\Models\CompetitionSubtype;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompetitionSubtype>
 */
class CompetitionSubtypeFactory extends Factory
{
    protected $model = CompetitionSubtype::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'en_name' => fake()->unique()->word(),
        ];
    }
}
