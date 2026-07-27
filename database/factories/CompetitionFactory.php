<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\CompetitionSubtype;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        return [
            'type_id' => CompetitionType::factory(),
            'subtype_id' => CompetitionSubtype::factory(),
            'organizer_id' => User::factory(),
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'start_date' => fake()->dateTimeBetween('+1 month', '+3 months'),
            'end_date' => fake()->dateTimeBetween('+3 months', '+6 months'),
            'location' => fake()->city(),
            'approval_status' => 'pending',
            'status' => 'draft',
        ];
    }
}
