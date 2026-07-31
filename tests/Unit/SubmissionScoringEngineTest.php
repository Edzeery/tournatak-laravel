<?php

namespace Tests\Unit;

use App\Models\Competition;
use App\Models\Judge;
use App\Models\Submission;
use App\Models\User;
use App\Services\SubmissionScoringEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionScoringEngineTest extends TestCase
{
    use RefreshDatabase;

    private SubmissionScoringEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new SubmissionScoringEngine;
    }

    public function test_supports_submission_basis(): void
    {
        $this->assertTrue($this->engine->supports('submission'));
        $this->assertFalse($this->engine->supports('match'));
    }

    public function test_calculate_ranking_averages_scores_and_sorts_desc(): void
    {
        $competition = Competition::factory()->submission()->create();
        $user1 = User::factory()->create(['name' => 'Alice']);
        $user2 = User::factory()->create(['name' => 'Bob']);

        $low = Submission::factory()->create(['competition_id' => $competition->id, 'user_id' => $user1->id]);
        $high = Submission::factory()->create(['competition_id' => $competition->id, 'user_id' => $user2->id]);

        $low->judgeScores()->create(['judge_id' => Judge::factory()->create()->id, 'score' => 60]);
        $low->judgeScores()->create(['judge_id' => Judge::factory()->create()->id, 'score' => 80]);
        $high->judgeScores()->create(['judge_id' => Judge::factory()->create()->id, 'score' => 90]);
        $high->judgeScores()->create(['judge_id' => Judge::factory()->create()->id, 'score' => 100]);

        $ranking = $this->engine->calculateRanking($competition);

        $this->assertCount(2, $ranking);
        $this->assertEquals($high->id, $ranking[0]['submission_id']);
        $this->assertEquals(95, $ranking[0]['score']);
        $this->assertEquals(70, $ranking[1]['score']);
        $this->assertEquals(2, $ranking[0]['scores_count']);
    }

    public function test_aggregate_score_supports_total_min_and_max(): void
    {
        $submission = Submission::factory()->create();
        $submission->judgeScores()->create(['judge_id' => Judge::factory()->create()->id, 'score' => 40]);
        $submission->judgeScores()->create(['judge_id' => Judge::factory()->create()->id, 'score' => 60]);
        $submission->judgeScores()->create(['judge_id' => Judge::factory()->create()->id, 'score' => 100]);

        $this->assertEquals(200, $this->engine->aggregateScore($submission, SubmissionScoringEngine::AGGREGATION_TOTAL));
        $this->assertEquals(40, $this->engine->aggregateScore($submission, SubmissionScoringEngine::AGGREGATION_MIN));
        $this->assertEquals(100, $this->engine->aggregateScore($submission, SubmissionScoringEngine::AGGREGATION_MAX));
        $this->assertEquals(round(200 / 3, 2), $this->engine->aggregateScore($submission));
    }

    public function test_aggregate_score_returns_zero_without_scores(): void
    {
        $submission = Submission::factory()->create();

        $this->assertEquals(0.0, $this->engine->aggregateScore($submission));
    }

    public function test_get_config_merges_format_scoring_with_defaults(): void
    {
        $competition = Competition::factory()->submission()->create([
            'format_config' => ['scoring' => ['max_score' => 50, 'aggregation' => 'total']],
        ]);

        $config = $this->engine->getConfig($competition);

        $this->assertEquals(50, $config['max_score']);
        $this->assertEquals('total', $config['aggregation']);
        $this->assertEquals(50, $this->engine->maxScore($competition));
    }

    public function test_calculate_ranking_uses_format_aggregation_by_default(): void
    {
        $competition = Competition::factory()->submission()->create([
            'format_config' => ['scoring' => ['aggregation' => 'total']],
        ]);
        $user = User::factory()->create();
        $submission = Submission::factory()->create(['competition_id' => $competition->id, 'user_id' => $user->id]);
        $submission->judgeScores()->create(['judge_id' => Judge::factory()->create()->id, 'score' => 20]);
        $submission->judgeScores()->create(['judge_id' => Judge::factory()->create()->id, 'score' => 30]);

        $ranking = $this->engine->calculateRanking($competition);

        $this->assertEquals(50, $ranking[0]['score']);
    }
}
