<?php

use App\Enums\SubmissionStatus;
use App\Livewire\Judge\JudgingPage;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Judge;
use App\Models\JudgeScore;
use App\Models\Submission;
use App\Models\User;
use Livewire\Livewire;

test('judge page is forbidden for users who are not assigned judges', function () {
    $user = User::factory()->create();
    $competition = Competition::factory()->submission()->create();

    Livewire::actingAs($user)
        ->test(JudgingPage::class, ['competition' => $competition])
        ->assertForbidden();
});

test('judge page is accessible for assigned judges and shows the current round', function () {
    $this->get('/lang/en')->assertRedirect();

    $competition = Competition::factory()->submission()->create();
    $round = CompetitionRound::factory()->create([
        'competition_id' => $competition->id,
        'number' => 1,
        'status' => CompetitionRound::STATUS_IN_PROGRESS,
    ]);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
        'status' => SubmissionStatus::UnderReview->value,
    ]);

    $judgeUser = User::factory()->create();
    Judge::factory()->create(['competition_id' => $competition->id, 'user_id' => $judgeUser->id]);

    Livewire::actingAs($judgeUser)
        ->test(JudgingPage::class, ['competition' => $competition])
        ->assertOk()
        ->assertSet('round_id', $round->id)
        ->assertSee($submission->title);
});

test('judge can save a score for a submission', function () {
    $competition = Competition::factory()->submission()->create();
    $round = CompetitionRound::factory()->create([
        'competition_id' => $competition->id,
        'number' => 1,
        'status' => CompetitionRound::STATUS_IN_PROGRESS,
    ]);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
    ]);

    $judgeUser = User::factory()->create();
    Judge::factory()->create(['competition_id' => $competition->id, 'user_id' => $judgeUser->id]);

    Livewire::actingAs($judgeUser)
        ->test(JudgingPage::class, ['competition' => $competition])
        ->set("scores.{$submission->id}.score", 85.5)
        ->set("scores.{$submission->id}.notes", 'Great work')
        ->call('saveScore', $submission->id)
        ->assertHasNoErrors();

    $judge = Judge::where('competition_id', $competition->id)->where('user_id', $judgeUser->id)->firstOrFail();

    $this->assertDatabaseHas('judge_scores', [
        'submission_id' => $submission->id,
        'judge_id' => $judge->id,
        'score' => 85.5,
        'notes' => 'Great work',
    ]);
});

test('judge can update an existing score and cannot exceed the max score', function () {
    $competition = Competition::factory()->submission()->create();
    $round = CompetitionRound::factory()->create([
        'competition_id' => $competition->id,
        'number' => 1,
        'status' => CompetitionRound::STATUS_IN_PROGRESS,
    ]);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
    ]);

    $judgeUser = User::factory()->create();
    $judge = Judge::factory()->create(['competition_id' => $competition->id, 'user_id' => $judgeUser->id]);
    JudgeScore::factory()->create(['submission_id' => $submission->id, 'judge_id' => $judge->id, 'score' => 40]);

    Livewire::actingAs($judgeUser)
        ->test(JudgingPage::class, ['competition' => $competition])
        ->set("scores.{$submission->id}.score", 150)
        ->call('saveScore', $submission->id)
        ->assertHasErrors("scores.{$submission->id}.score");

    Livewire::actingAs($judgeUser)
        ->test(JudgingPage::class, ['competition' => $competition])
        ->set("scores.{$submission->id}.score", 95)
        ->call('saveScore', $submission->id)
        ->assertHasNoErrors();

    expect($submission->judgeScores()->first()->score)->toBe(95.0)
        ->and($submission->judgeScores()->count())->toBe(1);
});

test('judge cannot score a submission from another competition', function () {
    $competition = Competition::factory()->submission()->create();
    $otherCompetition = Competition::factory()->submission()->create();
    $round = CompetitionRound::factory()->create([
        'competition_id' => $competition->id,
        'number' => 1,
        'status' => CompetitionRound::STATUS_IN_PROGRESS,
    ]);
    $foreignSubmission = Submission::factory()->create([
        'competition_id' => $otherCompetition->id,
        'round_id' => CompetitionRound::factory()->create(['competition_id' => $otherCompetition->id, 'number' => 1])->id,
    ]);

    $judgeUser = User::factory()->create();
    Judge::factory()->create(['competition_id' => $competition->id, 'user_id' => $judgeUser->id]);

    Livewire::actingAs($judgeUser)
        ->test(JudgingPage::class, ['competition' => $competition])
        ->set("scores.{$foreignSubmission->id}.score", 90)
        ->call('saveScore', $foreignSubmission->id)
        ->assertForbidden();
});

test('other judges averages are hidden by default and shown when configured', function () {
    $this->get('/lang/en')->assertRedirect();

    $competition = Competition::factory()->submission()->create();
    $round = CompetitionRound::factory()->create([
        'competition_id' => $competition->id,
        'number' => 1,
        'status' => CompetitionRound::STATUS_IN_PROGRESS,
    ]);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
    ]);

    $judgeUser = User::factory()->create();
    $judge = Judge::factory()->create(['competition_id' => $competition->id, 'user_id' => $judgeUser->id]);
    $otherJudge = Judge::factory()->create(['competition_id' => $competition->id]);
    JudgeScore::factory()->create(['submission_id' => $submission->id, 'judge_id' => $otherJudge->id, 'score' => 80]);

    Livewire::actingAs($judgeUser)
        ->test(JudgingPage::class, ['competition' => $competition])
        ->assertDontSee('Average Score');

    $config = $competition->format_config ?? [];
    $config['judging'] = ['hide_other_judges' => false];
    $competition->update(['format_config' => $config]);

    Livewire::actingAs($judgeUser)
        ->test(JudgingPage::class, ['competition' => $competition])
        ->assertSee('Average Score');
});
