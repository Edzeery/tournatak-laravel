<?php

use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Judge;
use App\Models\JudgeScore;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\QueryException;

test('competition round belongs to competition and has submissions', function () {
    $competition = Competition::factory()->submission()->create();
    $round = CompetitionRound::factory()->create(['competition_id' => $competition->id]);

    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
    ]);

    expect($round->competition->is($competition))->toBeTrue()
        ->and($round->submissions)->toHaveCount(1)
        ->and($submission->round->is($round))->toBeTrue()
        ->and($round->isCompleted())->toBeFalse();
});

test('competition round completed factory state', function () {
    $round = CompetitionRound::factory()->completed()->create();

    expect($round->status)->toBe(CompetitionRound::STATUS_COMPLETED)
        ->and($round->starts_at)->not->toBeNull()
        ->and($round->ends_at)->not->toBeNull()
        ->and($round->isCompleted())->toBeTrue();
});

test('submission belongs to competition and optional participants', function () {
    $competition = Competition::factory()->submission()->create();
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $individual = Submission::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user->id,
    ]);
    $teamSubmission = Submission::factory()->team()->create([
        'competition_id' => $competition->id,
        'team_id' => $team->id,
    ]);

    expect($individual->competition->is($competition))->toBeTrue()
        ->and($individual->isIndividualSubmission())->toBeTrue()
        ->and($individual->getParticipantName())->toBe($user->name)
        ->and($teamSubmission->isTeamSubmission())->toBeTrue()
        ->and($teamSubmission->getParticipantName())->toBe($team->name);
});

test('submission status enum and factory states', function () {
    $submission = Submission::factory()->underReview()->create();

    expect($submission->status)->toBe(SubmissionStatus::UnderReview)
        ->and(SubmissionStatus::Pending->label())->not->toBeEmpty()
        ->and(SubmissionStatus::UnderReview->label())->not->toBeEmpty()
        ->and(SubmissionStatus::Approved->label())->not->toBeEmpty()
        ->and(SubmissionStatus::Rejected->label())->not->toBeEmpty();
});

test('judge belongs to competition and user', function () {
    $competition = Competition::factory()->create();
    $user = User::factory()->create();
    $judge = Judge::factory()->lead()->create([
        'competition_id' => $competition->id,
        'user_id' => $user->id,
    ]);

    expect($judge->competition->is($competition))->toBeTrue()
        ->and($judge->user->is($user))->toBeTrue()
        ->and($judge->isLead())->toBeTrue()
        ->and($competition->judges)->toHaveCount(1);
});

test('judge is unique per competition', function () {
    $competition = Competition::factory()->create();
    $user = User::factory()->create();

    Judge::factory()->create(['competition_id' => $competition->id, 'user_id' => $user->id]);
    Judge::factory()->create(['competition_id' => $competition->id, 'user_id' => $user->id]);
})->throws(QueryException::class);

test('judge score belongs to submission and judge', function () {
    $submission = Submission::factory()->create();
    $judge = Judge::factory()->create();
    $score = JudgeScore::factory()->create([
        'submission_id' => $submission->id,
        'judge_id' => $judge->id,
        'score' => 85.5,
    ]);

    expect($score->submission->is($submission))->toBeTrue()
        ->and($score->judge->is($judge))->toBeTrue()
        ->and($score->score)->toBe(85.5)
        ->and($submission->judgeScores)->toHaveCount(1);
});

test('competition policy grants judge ability to assigned judges', function () {
    $competition = Competition::factory()->create();
    $judge = Judge::factory()->create(['competition_id' => $competition->id]);

    $this->assertTrue($judge->user->can('judge', $competition));
});

test('competition policy grants judge ability to owning organizer', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');
    $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);

    $this->assertTrue($organizer->can('judge', $competition));
});

test('competition policy grants judge ability to admin', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $competition = Competition::factory()->create();

    $this->assertTrue($admin->can('judge', $competition));
});

test('competition policy denies judge ability to unrelated user', function () {
    $user = User::factory()->create();
    $competition = Competition::factory()->create();

    $this->assertFalse($user->can('judge', $competition));
});

test('competition policy denies judge ability to unrelated organizer', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');
    $competition = Competition::factory()->create(['organizer_id' => User::factory()->create()->id]);

    $this->assertFalse($organizer->can('judge', $competition));
});
