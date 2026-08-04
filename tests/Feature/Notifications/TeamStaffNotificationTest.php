<?php

use App\Livewire\Admin\Teams\TeamStaffPage;
use App\Models\Team;
use App\Models\TeamStaff;
use App\Models\User;
use App\Models\UserNotification;
use Livewire\Livewire;

test('adding a staff member notifies the added user with the team and role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $coach = User::factory()->create();
    $team = Team::factory()->create(['captain_id' => $admin->id]);

    Livewire::actingAs($admin)
        ->test(TeamStaffPage::class, ['team' => $team])
        ->set('staffForm.user_id', $coach->id)
        ->set('staffForm.staff_role', 'head_coach')
        ->call('saveStaff')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('notifications', [
        'user_id' => $coach->id,
        'title' => __('app.team_staff_assigned_title'),
        'message' => __('app.team_staff_assigned_notification', [
            'team' => $team->name,
            'role' => TeamStaff::STAFF_ROLES['head_coach'],
        ]),
        'link' => route('teams.show', $team->id),
    ]);
    expect(UserNotification::where('user_id', $admin->id)->count())->toBe(0);
});

test('editing an existing staff record does not notify', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $coach = User::factory()->create();
    $team = Team::factory()->create(['captain_id' => $admin->id]);
    $staff = TeamStaff::create([
        'team_id' => $team->id,
        'user_id' => $coach->id,
        'staff_role' => 'assistant_coach',
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(TeamStaffPage::class, ['team' => $team])
        ->call('editStaff', $staff->id)
        ->set('staffForm.staff_role', 'head_coach')
        ->call('saveStaff')
        ->assertHasNoErrors();

    expect(UserNotification::count())->toBe(0);
});
