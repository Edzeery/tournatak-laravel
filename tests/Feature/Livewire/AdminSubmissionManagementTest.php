<?php

use App\Enums\SubmissionStatus;
use App\Livewire\Admin\Competitions\CompetitionJudgingPage;
use App\Livewire\Admin\Competitions\RoundsPage;
use App\Livewire\Admin\Competitions\SubmissionsPage;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Judge;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

test('organizer can create a round from the rounds page', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);

    Livewire::actingAs($organizer)
        ->test(RoundsPage::class, ['competition' => $competition])
        ->assertOk()
        ->set('name', 'Qualifying Round')
        ->set('number', 1)
        ->set('starts_at', '2026-09-01 09:00')
        ->set('ends_at', '2026-09-01 18:00')
        ->call('create')
        ->assertHasNoErrors()
        ->assertSet('name', '');

    $this->assertDatabaseHas('competition_rounds', [
        'competition_id' => $competition->id,
        'name' => 'Qualifying Round',
        'number' => 1,
        'status' => CompetitionRound::STATUS_SCHEDULED,
    ]);
});

test('rounds page rejects duplicate round numbers', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);
    CompetitionRound::factory()->create(['competition_id' => $competition->id, 'number' => 1]);

    Livewire::actingAs($organizer)
        ->test(RoundsPage::class, ['competition' => $competition])
        ->set('name', 'Duplicate')
        ->set('number', 1)
        ->call('create')
        ->assertHasErrors(['number']);
});

test('organizer can create a submission for a registered team', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);
    $round = CompetitionRound::factory()->create(['competition_id' => $competition->id, 'number' => 1]);

    $team = Team::factory()->create();
    $competition->teams()->attach($team->id, ['status' => Registration::STATUS_APPROVED]);

    Livewire::actingAs($organizer)
        ->test(SubmissionsPage::class, ['competition' => $competition])
        ->assertOk()
        ->set('newRoundId', $round->id)
        ->set('newParticipantType', 'team')
        ->set('newParticipantId', $team->id)
        ->set('newTitle', 'Innovative Solution')
        ->set('newDescription', 'A solution description')
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('submissions', [
        'competition_id' => $competition->id,
        'round_id' => $round->id,
        'participant_type' => 'team',
        'team_id' => $team->id,
        'title' => 'Innovative Solution',
        'status' => SubmissionStatus::Pending->value,
    ]);
});

test('organizer can update a submission and change its status', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);
    $round = CompetitionRound::factory()->create(['competition_id' => $competition->id, 'number' => 1]);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
    ]);

    Livewire::actingAs($organizer)
        ->test(SubmissionsPage::class, ['competition' => $competition])
        ->call('startEdit', $submission->id)
        ->assertSet('editSubmissionId', $submission->id)
        ->set('editTitle', 'Updated Solution Title')
        ->set('editStatus', SubmissionStatus::Approved->value)
        ->call('update')
        ->assertHasNoErrors()
        ->assertSet('editSubmissionId', null);

    $this->assertDatabaseHas('submissions', [
        'id' => $submission->id,
        'title' => 'Updated Solution Title',
        'status' => SubmissionStatus::Approved->value,
    ]);
});

test('organizer can set submission status directly from the list', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);
    $round = CompetitionRound::factory()->create(['competition_id' => $competition->id, 'number' => 1]);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
    ]);

    Livewire::actingAs($organizer)
        ->test(SubmissionsPage::class, ['competition' => $competition])
        ->call('setStatus', $submission->id, SubmissionStatus::Rejected->value)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('submissions', [
        'id' => $submission->id,
        'status' => SubmissionStatus::Rejected->value,
    ]);
});

test('organizer can assign and remove judges from the judging page', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);
    $judgeUser = User::factory()->create();

    Livewire::actingAs($organizer)
        ->test(CompetitionJudgingPage::class, ['competition' => $competition])
        ->assertOk()
        ->set('newJudgeUserId', $judgeUser->id)
        ->set('newJudgeLead', true)
        ->call('addJudge')
        ->assertHasNoErrors();

    $judge = Judge::where('competition_id', $competition->id)->where('user_id', $judgeUser->id)->firstOrFail();
    expect($judge->isLead())->toBeTrue();

    Livewire::actingAs($organizer)
        ->test(CompetitionJudgingPage::class, ['competition' => $competition])
        ->call('removeJudge', $judge->id)
        ->assertHasNoErrors();

    expect(Judge::find($judge->id))->toBeNull();
});

test('judging page prevents assigning the same judge twice', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);
    $judgeUser = User::factory()->create();
    Judge::factory()->create(['competition_id' => $competition->id, 'user_id' => $judgeUser->id]);

    Livewire::actingAs($organizer)
        ->test(CompetitionJudgingPage::class, ['competition' => $competition])
        ->set('newJudgeUserId', $judgeUser->id)
        ->call('addJudge')
        ->assertHasErrors(['newJudgeUserId']);
});

test('organizer can save judging settings', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $competition = Competition::factory()->submission()->create(['organizer_id' => $organizer->id]);

    Livewire::actingAs($organizer)
        ->test(CompetitionJudgingPage::class, ['competition' => $competition])
        ->set('hideOtherJudges', false)
        ->call('saveSettings')
        ->assertHasNoErrors();

    $this->assertFalse($competition->refresh()->format_config['judging']['hide_other_judges']);
});

test('submission management pages are forbidden for unrelated organizers', function () {
    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $competition = Competition::factory()->submission()->create();

    Livewire::actingAs($organizer)
        ->test(RoundsPage::class, ['competition' => $competition])
        ->assertForbidden();

    Livewire::actingAs($organizer)
        ->test(SubmissionsPage::class, ['competition' => $competition])
        ->assertForbidden();

    Livewire::actingAs($organizer)
        ->test(CompetitionJudgingPage::class, ['competition' => $competition])
        ->assertForbidden();
});
