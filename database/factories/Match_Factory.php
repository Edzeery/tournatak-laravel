<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Match_;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class Match_Factory extends Factory
{
    protected $model = Match_::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'team1_id' => Team::factory(),
            'team2_id' => Team::factory(),
            'match_date' => $this->faker->dateTimeThisYear,
            'score_team1' => $this->faker->numberBetween(0, 5),
            'score_team2' => $this->faker->numberBetween(0, 5),
            'status' => $this->faker->randomElement(['scheduled', 'in_progress', 'completed']),
        ];
    }
}
