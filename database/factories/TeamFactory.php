<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'logo' => null,
            'captain_id' => null,
            'points' => 0,
        ];
    }

    public function withCaptain(): static
    {
        return $this->state(fn (array $attributes) => [
            'captain_id' => User::factory()->create()->id,
        ]);
    }
}
