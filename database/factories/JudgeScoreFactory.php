<?php

namespace Database\Factories;

use App\Models\Judge;
use App\Models\JudgeScore;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JudgeScore>
 */
class JudgeScoreFactory extends Factory
{
    protected $model = JudgeScore::class;

    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'judge_id' => Judge::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
            'notes' => fake()->sentence(),
        ];
    }
}
