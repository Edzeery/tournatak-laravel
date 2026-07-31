<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\CompetitionRound;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompetitionRound>
 */
class CompetitionRoundFactory extends Factory
{
    protected $model = CompetitionRound::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'name' => fake()->unique()->words(2, true),
            'number' => fake()->unique()->numberBetween(1, 20),
            'status' => CompetitionRound::STATUS_SCHEDULED,
            'starts_at' => null,
            'ends_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => CompetitionRound::STATUS_COMPLETED,
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->subDay(),
        ]);
    }
}
