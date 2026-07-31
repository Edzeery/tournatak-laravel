<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Judge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Judge>
 */
class JudgeFactory extends Factory
{
    protected $model = Judge::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'user_id' => User::factory(),
            'is_lead' => false,
        ];
    }

    public function lead(): static
    {
        return $this->state(fn () => ['is_lead' => true]);
    }
}
