<?php

use App\Livewire\Admin\Registrations\CreateRegistrationPage;
use App\Livewire\Admin\Registrations\CreateTeamRegistrationPage;
use App\Livewire\Admin\Registrations\RegistrationsPage;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\Registration;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

// ─── Access control ───

test('admin can access registrations index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin)->get('/panel/registrations')->assertStatus(200);
});

test('guest cannot access registrations index', function () {
    $this->get('/panel/registrations')->assertRedirect('/login');
});

test('non-admin user cannot access registrations index', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $this->actingAs($user)->get('/panel/registrations')->assertForbidden();
});

test('admin can access create individual registration page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin)->get('/panel/registrations/create/individual')->assertStatus(200);
});

test('admin can access create team registration page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin)->get('/panel/registrations/create/team')->assertStatus(200);
});

// ─── RegistrationsPage ───

test('registrations page shows registration data', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $registration = Registration::factory()->create();

    Livewire::actingAs($admin)->test(RegistrationsPage::class)
        ->assertSee($registration->team->name);
});

test('registrations page filters by participant type', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $individualType = CompetitionType::factory()->create(['participant_type' => 'individual']);
    $competition = Competition::factory()->create(['type_id' => $individualType->id, 'approval_status' => 'approved']);
    $user = User::factory()->create();
    $indivReg = Registration::factory()->individual()->create([
        'competition_id' => $competition->id,
        'user_id' => $user->id,
        'team_id' => null,
    ]);
    $teamReg = Registration::factory()->create();

    Livewire::actingAs($admin)->test(RegistrationsPage::class)
        ->set('participantTypeFilter', 'individual')
        ->assertSee($indivReg->user->name)
        ->assertDontSee($teamReg->team->name);
});

test('registrations page filters by status', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $approved = Registration::factory()->approved()->create();

    Livewire::actingAs($admin)->test(RegistrationsPage::class)
        ->set('statusFilter', 'approved')
        ->assertSee($approved->team->name);
});

test('admin can approve registration', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $registration = Registration::factory()->create(['status' => Registration::STATUS_PENDING]);

    Livewire::actingAs($admin)->test(RegistrationsPage::class)
        ->call('approve', $registration->id);

    $this->assertDatabaseHas('registrations', [
        'id' => $registration->id,
        'status' => Registration::STATUS_APPROVED,
    ]);
});

test('admin can reject registration', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $registration = Registration::factory()->create(['status' => Registration::STATUS_PENDING]);

    Livewire::actingAs($admin)->test(RegistrationsPage::class)
        ->call('reject', $registration->id);

    $this->assertDatabaseHas('registrations', [
        'id' => $registration->id,
        'status' => Registration::STATUS_REJECTED,
    ]);
});

test('admin can delete registration', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $registration = Registration::factory()->create();

    Livewire::actingAs($admin)->test(RegistrationsPage::class)
        ->call('delete', $registration->id);

    $this->assertDatabaseMissing('registrations', ['id' => $registration->id]);
});

// ─── CreateRegistrationPage (individual) ───

test('create individual registration page renders competitions dropdown', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $type = CompetitionType::factory()->create(['participant_type' => 'individual']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);

    Livewire::actingAs($admin)->test(CreateRegistrationPage::class)
        ->assertSee($competition->name);
});

test('create individual registration excludes team-only competitions', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teamType = CompetitionType::factory()->create(['participant_type' => 'team']);
    $competition = Competition::factory()->create([
        'type_id' => $teamType->id,
        'approval_status' => 'approved',
    ]);

    Livewire::actingAs($admin)->test(CreateRegistrationPage::class)
        ->assertDontSee($competition->name);
});

test('admin can create individual registration', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $type = CompetitionType::factory()->create(['participant_type' => 'individual']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);
    $user = User::factory()->create();

    Livewire::actingAs($admin)->test(CreateRegistrationPage::class)
        ->set('competition_id', $competition->id)
        ->set('user_id', $user->id)
        ->call('store')
        ->assertRedirect(route('admin.registrations.index'));

    $this->assertDatabaseHas('registrations', [
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_INDIVIDUAL,
        'user_id' => $user->id,
        'status' => Registration::STATUS_APPROVED,
    ]);
});

test('create individual registration fails with missing fields', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)->test(CreateRegistrationPage::class)
        ->call('store')
        ->assertHasErrors(['competition_id', 'user_id']);
});

test('create individual registration prevents duplicates', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $type = CompetitionType::factory()->create(['participant_type' => 'individual']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);
    $user = User::factory()->create();
    Registration::create([
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_INDIVIDUAL,
        'user_id' => $user->id,
        'status' => Registration::STATUS_APPROVED,
    ]);

    Livewire::actingAs($admin)->test(CreateRegistrationPage::class)
        ->set('competition_id', $competition->id)
        ->set('user_id', $user->id)
        ->call('store')
        ->assertNoRedirect();
});

// ─── CreateTeamRegistrationPage ───

test('create team registration page renders competitions dropdown', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $type = CompetitionType::factory()->create(['participant_type' => 'team']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);

    Livewire::actingAs($admin)->test(CreateTeamRegistrationPage::class)
        ->assertSee($competition->name);
});

test('create team registration excludes individual-only competitions', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $individualType = CompetitionType::factory()->create(['participant_type' => 'individual']);
    $competition = Competition::factory()->create([
        'type_id' => $individualType->id,
        'approval_status' => 'approved',
    ]);

    Livewire::actingAs($admin)->test(CreateTeamRegistrationPage::class)
        ->assertDontSee($competition->name);
});

test('admin can create team registration', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $type = CompetitionType::factory()->create(['participant_type' => 'team']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);
    $team = Team::factory()->create();

    Livewire::actingAs($admin)->test(CreateTeamRegistrationPage::class)
        ->set('competition_id', $competition->id)
        ->set('team_id', $team->id)
        ->call('store')
        ->assertRedirect(route('admin.registrations.index'));

    $this->assertDatabaseHas('registrations', [
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_TEAM,
        'team_id' => $team->id,
        'status' => Registration::STATUS_APPROVED,
    ]);
});

test('create team registration fails with missing fields', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)->test(CreateTeamRegistrationPage::class)
        ->call('store')
        ->assertHasErrors(['competition_id', 'team_id']);
});

test('create team registration prevents duplicates', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $type = CompetitionType::factory()->create(['participant_type' => 'team']);
    $competition = Competition::factory()->create([
        'type_id' => $type->id,
        'approval_status' => 'approved',
    ]);
    $team = Team::factory()->create();
    Registration::create([
        'competition_id' => $competition->id,
        'participant_type' => Registration::PARTICIPANT_TEAM,
        'team_id' => $team->id,
        'status' => Registration::STATUS_APPROVED,
    ]);

    Livewire::actingAs($admin)->test(CreateTeamRegistrationPage::class)
        ->set('competition_id', $competition->id)
        ->set('team_id', $team->id)
        ->call('store')
        ->assertNoRedirect();
});
