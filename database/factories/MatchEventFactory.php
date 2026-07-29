<?php

namespace Database\Factories;

use App\Models\MatchEvent;
use App\Models\Match_;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchEventFactory extends Factory
{
    protected $model = MatchEvent::class;

    public function definition(): array
    {
        return [
            'match_id' => Match_::factory(),
            'team_id' => Team::factory(),
            'player_id' => Player::factory(),
            'event_type' => $this->faker->randomElement(array_keys(MatchEvent::EVENT_TYPES)),
            'minute' => $this->faker->numberBetween(1, 90),
            'added_time' => 0,
            'description' => $this->faker->optional()->sentence(),
            'related_player_id' => null,
        ];
    }

    public function goal(): static
    {
        return $this->state(fn(array $attrs) => ['event_type' => 'goal']);
    }

    public function yellowCard(): static
    {
        return $this->state(fn(array $attrs) => ['event_type' => 'yellow_card']);
    }

    public function redCard(): static
    {
        return $this->state(fn(array $attrs) => ['event_type' => 'red_card']);
    }
}
