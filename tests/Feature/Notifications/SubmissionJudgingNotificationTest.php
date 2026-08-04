<?php

use App\Enums\SubmissionStatus;
use App\Livewire\Admin\Competitions\CompetitionJudgingPage;
use App\Livewire\Admin\Competitions\SubmissionsPage;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use App\Models\UserNotification;
use Livewire\Livewire;

test('assigning a judge notifies the judge with a judging link', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');
    $judgeUser = User::factory()->create();

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);

    Livewire::actingAs($organizer)
        ->test(CompetitionJudgingPage::class, ['competition' => $competition])
        ->set('newJudgeUserId', $judgeUser->id)
        ->call('addJudge');

    $this->assertDatabaseHas('notifications', [
        'user_id' => $judgeUser->id,
        'title' => __('app.judge_assigned_title'),
        'message' => __('app.judge_assigned_notification', ['competition' => $competition->name]),
        'link' => route('judge.competitions.show', $competition),
    ]);
    expect(UserNotification::where('user_id', $organizer->id)->count())->toBe(0);
});

test('approving a submission notifies the individual submitter', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');
    $applicant = User::factory()->create();

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);
    $round = CompetitionRound::factory()->create(['competition_id' => $competition->id, 'number' => 1]);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
        'user_id' => $applicant->id,
    ]);

    Livewire::actingAs($organizer)
        ->test(SubmissionsPage::class, ['competition' => $competition])
        ->call('startEdit', $submission->id)
        ->set('editStatus', SubmissionStatus::Approved->value)
        ->call('update');

    $this->assertDatabaseHas('notifications', [
        'user_id' => $applicant->id,
        'title' => __('app.submission_approved_notification'),
        'message' => $submission->title,
        'link' => route('competitions.show', $competition),
    ]);
    expect(UserNotification::count())->toBe(1);
});

test('rejecting a team submission notifies the team captain', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');
    $captain = User::factory()->create();

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);
    $round = CompetitionRound::factory()->create(['competition_id' => $competition->id, 'number' => 1]);
    $team = Team::factory()->create(['captain_id' => $captain->id]);
    $submission = Submission::factory()->team()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
        'team_id' => $team->id,
    ]);

    Livewire::actingAs($organizer)
        ->test(SubmissionsPage::class, ['competition' => $competition])
        ->call('setStatus', $submission->id, SubmissionStatus::Rejected->value);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $captain->id,
        'title' => __('app.submission_rejected_notification'),
        'message' => $submission->title,
        'link' => route('competitions.show', $competition),
    ]);
    expect(UserNotification::count())->toBe(1);
});

test('updating a submission without changing status does not notify', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');
    $applicant = User::factory()->create();

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);
    $round = CompetitionRound::factory()->create(['competition_id' => $competition->id, 'number' => 1]);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
        'user_id' => $applicant->id,
    ]);

    Livewire::actingAs($organizer)
        ->test(SubmissionsPage::class, ['competition' => $competition])
        ->call('startEdit', $submission->id)
        ->set('editTitle', 'Just a title tweak')
        ->call('update');

    expect(UserNotification::count())->toBe(0);
});
