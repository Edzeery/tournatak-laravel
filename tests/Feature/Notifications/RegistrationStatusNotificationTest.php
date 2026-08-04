<?php

use App\Livewire\Admin\Registrations\RegistrationsPage;
use App\Models\Competition;
use App\Models\Registration;
use App\Models\Team;
use App\Models\User;
use App\Models\UserNotification;
use Livewire\Livewire;

test('approving an individual registration notifies the registered user and the organizer', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $organizer = User::factory()->create();
    $applicant = User::factory()->create();
    $unrelated = User::factory()->create();
    $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);
    $registration = Registration::factory()->individual()->create([
        'competition_id' => $competition->id,
        'user_id' => $applicant->id,
    ]);

    Livewire::actingAs($admin)->test(RegistrationsPage::class)
        ->call('approve', $registration->id);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $applicant->id,
        'title' => __('app.registration_approved_notification'),
        'message' => $competition->name,
        'link' => route('user.registrations'),
    ]);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $organizer->id,
        'title' => __('app.registration_approved_notification'),
        'message' => $competition->name,
        'link' => route('admin.registrations.index'),
    ]);
    expect(UserNotification::where('user_id', $admin->id)->count())->toBe(0);
    expect(UserNotification::where('user_id', $unrelated->id)->count())->toBe(0);
});

test('rejecting an individual registration notifies the registered user and the organizer', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $organizer = User::factory()->create();
    $applicant = User::factory()->create();
    $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);
    $registration = Registration::factory()->individual()->create([
        'competition_id' => $competition->id,
        'user_id' => $applicant->id,
    ]);

    Livewire::actingAs($admin)->test(RegistrationsPage::class)
        ->call('reject', $registration->id);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $applicant->id,
        'title' => __('app.registration_rejected_notification'),
    ]);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $organizer->id,
        'title' => __('app.registration_rejected_notification'),
    ]);
});

test('approving a team registration notifies the team captain instead of the registered user', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $organizer = User::factory()->create();
    $captain = User::factory()->create();
    $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);
    $team = Team::factory()->create(['captain_id' => $captain->id]);
    $registration = Registration::factory()->create([
        'competition_id' => $competition->id,
        'team_id' => $team->id,
    ]);

    Livewire::actingAs($admin)->test(RegistrationsPage::class)
        ->call('approve', $registration->id);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $captain->id,
        'title' => __('app.registration_approved_notification'),
    ]);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $organizer->id,
        'title' => __('app.registration_approved_notification'),
    ]);
    expect(UserNotification::count())->toBe(2);
});
