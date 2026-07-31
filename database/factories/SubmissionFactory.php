<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'participant_type' => Submission::PARTICIPANT_INDIVIDUAL,
            'team_id' => null,
            'user_id' => User::factory(),
            'player_id' => null,
            'round_id' => null,
            'title' => fake()->unique()->sentence(4),
            'description' => fake()->paragraph(),
            'file_path' => null,
            'status' => SubmissionStatus::Pending->value,
        ];
    }

    public function team(): static
    {
        return $this->state(fn () => [
            'participant_type' => Submission::PARTICIPANT_TEAM,
            'team_id' => Team::factory(),
            'user_id' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => SubmissionStatus::Approved->value]);
    }

    public function underReview(): static
    {
        return $this->state(fn () => ['status' => SubmissionStatus::UnderReview->value]);
    }
}
