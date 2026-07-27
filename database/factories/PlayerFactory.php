<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Player>
 */
class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_id' => Team::factory(),
            'number' => fake()->numberBetween(1, 99),
            'position_text' => fake()->randomElement(['Goalkeeper', 'Defender', 'Midfielder', 'Forward']),
            'image' => null,
            'position_id' => null,
            'date_of_birth' => fake()->dateTimeBetween('-30 years', '-18 years'),
            'nationality' => fake()->country(),
            'height' => fake()->numberBetween(160, 200),
            'weight' => fake()->numberBetween(60, 90),
            'foot' => fake()->randomElement(['left', 'right', 'both']),
            'sport_type' => fake()->randomElement(['football', 'futsal']),
            'bio' => fake()->sentence(),
            'is_captain' => false,
        ];
    }
}
