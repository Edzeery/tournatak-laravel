<?php

use App\Livewire\User\RegistrationsPage;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\Registration;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

test('user can register as individual to competition', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $type = CompetitionType::factory()->create(['participant_type' => 'individual']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->set('participantType', 'individual')
        ->set('competition_id', $competition->id)
        ->call('register');

    $this->assertDatabaseHas('registrations', [
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_INDIVIDUAL,
        'user_id' => $user->id,
        'status' => Registration::STATUS_PENDING,
    ]);
});

test('user can register team to competition', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $team = Team::factory()->create(['captain_id' => $user->id]);
    $type = CompetitionType::factory()->create(['participant_type' => 'team']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->set('participantType', 'team')
        ->set('competition_id', $competition->id)
        ->set('team_id', $team->id)
        ->call('register');

    $this->assertDatabaseHas('registrations', [
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_TEAM,
        'team_id' => $team->id,
        'status' => Registration::STATUS_PENDING,
    ]);
});

test('user cannot register individual to team-only competition', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $type = CompetitionType::factory()->create(['participant_type' => 'team']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->assertDontSee($competition->name);
});

test('user cannot register team to individual-only competition', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $team = Team::factory()->create(['captain_id' => $user->id]);
    $type = CompetitionType::factory()->create(['participant_type' => 'individual']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->set('participantType', 'team')
        ->assertDontSee($competition->name);
});

test('user cannot register for competition twice', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $type = CompetitionType::factory()->create(['participant_type' => 'individual']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);
    Registration::create([
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_INDIVIDUAL,
        'user_id' => $user->id,
        'status' => Registration::STATUS_APPROVED,
    ]);

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->set('participantType', 'individual')
        ->set('competition_id', $competition->id)
        ->call('register');

    $this->assertDatabaseCount('registrations', 1);
});

test('user cannot register team for competition twice', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $team = Team::factory()->create(['captain_id' => $user->id]);
    $type = CompetitionType::factory()->create(['participant_type' => 'team']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);
    Registration::create([
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_TEAM,
        'team_id' => $team->id,
        'status' => Registration::STATUS_APPROVED,
    ]);

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->set('participantType', 'team')
        ->set('competition_id', $competition->id)
        ->set('team_id', $team->id)
        ->call('register');

    $this->assertDatabaseCount('registrations', 1);
});

test('user sees their individual registrations on page', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $type = CompetitionType::factory()->create(['participant_type' => 'individual']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);
    Registration::create([
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_INDIVIDUAL,
        'user_id' => $user->id,
        'status' => Registration::STATUS_PENDING,
    ]);

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->assertSee($competition->name);
});

test('user sees their team registrations on page', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $team = Team::factory()->create(['captain_id' => $user->id]);
    $type = CompetitionType::factory()->create(['participant_type' => 'team']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);
    Registration::create([
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_TEAM,
        'team_id' => $team->id,
        'status' => Registration::STATUS_PENDING,
    ]);

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->assertSee($competition->name)
        ->assertSee($team->name);
});

test('registration form validation requires competition_id', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->call('register')
        ->assertHasErrors(['competition_id']);
});

test('registration form requires team_id for team registration', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    Livewire::actingAs($user)->test(RegistrationsPage::class)
        ->set('participantType', 'team')
        ->call('register')
        ->assertHasErrors(['team_id']);
});
