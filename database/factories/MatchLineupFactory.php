<?php

namespace Database\Factories;

use App\Models\Match_;
use App\Models\MatchLineup;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchLineupFactory extends Factory
{
    protected $model = MatchLineup::class;

    public function definition(): array
    {
        return [
            'match_id' => Match_::factory(),
            'player_id' => Player::factory(),
            'team_id' => Team::factory(),
            'position_id' => null,
            'is_starter' => $this->faker->boolean(80),
            'jersey_number' => $this->faker->numberBetween(1, 99),
            'is_captain' => $this->faker->boolean(10),
        ];
    }
}
