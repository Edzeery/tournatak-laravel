<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Registration;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationFactory extends Factory
{
    protected $model = Registration::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'participant_type' => Registration::PARTICIPANT_TEAM,
            'team_id' => Team::factory(),
            'status' => Registration::STATUS_PENDING,
        ];
    }

    public function individual(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'participant_type' => Registration::PARTICIPANT_INDIVIDUAL,
                'team_id' => null,
            ];
        });
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => Registration::STATUS_APPROVED]);
    }
}
