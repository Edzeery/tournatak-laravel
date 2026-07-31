<?php

use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\CompetitionDomain;
use App\Models\CompetitionRound;
use App\Models\Judge;
use App\Models\JudgeScore;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use App\Services\SubmissionScoringEngine;

test('submission competition public detail renders domain tabs', function () {
    $this->get('/lang/en')->assertRedirect();

    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $hackathon = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

    $competition = Competition::factory()->create([
        'organizer_id' => $organizer->id,
        'domain_id' => $hackathon->id,
        'status' => 'ongoing',
        'approval_status' => 'approved',
    ]);

    CompetitionRound::factory()->create([
        'competition_id' => $competition->id,
        'number' => 1,
    ]);

    $this->get(route('competitions.show', $competition))
        ->assertOk()
        ->assertSee($competition->name)
        ->assertSee('Overview')
        ->assertSee('Rounds & Submissions')
        ->assertSee('Results')
        ->assertSee('No submissions for this round');
});

test('submission competition results page shows engine ranking after judging', function () {
    $this->get('/lang/en')->assertRedirect();

    $organizer = User::factory()->create();
    $organizer->assignRole('organizer');

    $hackathon = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

    $competition = Competition::factory()->create([
        'organizer_id' => $organizer->id,
        'domain_id' => $hackathon->id,
        'status' => 'ongoing',
        'approval_status' => 'approved',
    ]);

    $round = CompetitionRound::factory()->create([
        'competition_id' => $competition->id,
        'number' => 1,
        'status' => CompetitionRound::STATUS_IN_PROGRESS,
    ]);

    $teamA = Team::factory()->create();
    $teamB = Team::factory()->create();
    $competition->teams()->attach([$teamA->id, $teamB->id], ['status' => Registration::STATUS_APPROVED]);

    $subA = Submission::factory()->team()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
        'team_id' => $teamA->id,
        'title' => 'Team A Proposal',
        'status' => SubmissionStatus::Approved->value,
    ]);
    $subB = Submission::factory()->team()->create([
        'competition_id' => $competition->id,
        'round_id' => $round->id,
        'team_id' => $teamB->id,
        'title' => 'Team B Proposal',
        'status' => SubmissionStatus::Approved->value,
    ]);

    $judge = Judge::factory()->create(['competition_id' => $competition->id]);

    JudgeScore::factory()->create(['submission_id' => $subA->id, 'judge_id' => $judge->id, 'score' => 90]);
    JudgeScore::factory()->create(['submission_id' => $subB->id, 'judge_id' => $judge->id, 'score' => 70]);

    $ranking = app(SubmissionScoringEngine::class)->calculateRanking($competition);

    expect($ranking[0]['participant_name'])->toBe($teamA->name)
        ->and($ranking[0]['score'])->toBe(90.0)
        ->and($ranking[1]['participant_name'])->toBe($teamB->name)
        ->and($ranking[1]['score'])->toBe(70.0);

    $this->get(route('competitions.show', $competition))
        ->assertOk()
        ->assertSee('Team A Proposal')
        ->assertSee('Team B Proposal')
        ->assertSee('90.00')
        ->assertSee('70.00')
        ->assertSee('100.00')
        ->assertSeeInOrder(['90.00', '70.00']);
});

test('match domain competition still renders the classic detail page', function () {
    $this->get('/lang/en')->assertRedirect();

    $competition = Competition::factory()->sports()->create();

    $this->get(route('competitions.show', $competition))
        ->assertOk()
        ->assertSee($competition->name)
        ->assertDontSee('Rounds & Submissions');
});
