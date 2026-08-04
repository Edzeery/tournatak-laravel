<?php

use App\Livewire\Admin\Players\CreatePlayerPage;
use App\Livewire\Admin\Players\EditPlayerPage;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamStaff;
use App\Models\User;
use App\Models\UserNotification;
use Livewire\Livewire;

test('creating a player notifies the player, the team captain and the team staff', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $playerUser = User::factory()->create();
    $captain = User::factory()->create();
    $coach = User::factory()->create();
    $team = Team::factory()->create(['captain_id' => $captain->id]);
    TeamStaff::create([
        'team_id' => $team->id,
        'user_id' => $coach->id,
        'staff_role' => 'head_coach',
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(CreatePlayerPage::class)
        ->set('user_id', $playerUser->id)
        ->set('team_id', $team->id)
        ->call('store')
        ->assertHasNoErrors();

    $player = Player::where('user_id', $playerUser->id)->first();

    expect(UserNotification::count())->toBe(3);
    expect(UserNotification::where('user_id', $playerUser->id)->first()->title)
        ->toBe(__('app.player_assigned_title'));
    expect(UserNotification::where('user_id', $playerUser->id)->first()->message)
        ->toBe(__('app.player_joined_notification', ['player' => $player->name, 'team' => $team->name]));
    expect(UserNotification::where('user_id', $playerUser->id)->first()->link)
        ->toBe(route('teams.show', $team->id));
    expect(UserNotification::where('user_id', $captain->id)->count())->toBe(1);
    expect(UserNotification::where('user_id', $coach->id)->count())->toBe(1);
    expect(UserNotification::where('user_id', $admin->id)->count())->toBe(0);
});

test('transferring a player to another team notifies the new team, the old team and the player', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $playerUser = User::factory()->create();
    $captainA = User::factory()->create();
    $coachA = User::factory()->create();
    $captainB = User::factory()->create();
    $coachB = User::factory()->create();

    $teamA = Team::factory()->create(['captain_id' => $captainA->id]);
    TeamStaff::create([
        'team_id' => $teamA->id,
        'user_id' => $coachA->id,
        'staff_role' => 'head_coach',
        'is_active' => true,
    ]);
    $teamB = Team::factory()->create(['captain_id' => $captainB->id]);
    TeamStaff::create([
        'team_id' => $teamB->id,
        'user_id' => $coachB->id,
        'staff_role' => 'head_coach',
        'is_active' => true,
    ]);

    $player = Player::factory()->create(['user_id' => $playerUser->id, 'team_id' => $teamA->id]);

    Livewire::actingAs($admin)
        ->test(EditPlayerPage::class, ['player' => $player])
        ->set('team_id', $teamB->id)
        ->call('update')
        ->assertHasNoErrors();

    $player->refresh();

    expect($player->team_id)->toBe($teamB->id);
    expect(UserNotification::where('user_id', $playerUser->id)->first()->message)
        ->toBe(__('app.player_joined_notification', ['player' => $player->name, 'team' => $teamB->name]));
    expect(UserNotification::where('user_id', $captainB->id)->count())->toBe(1);
    expect(UserNotification::where('user_id', $coachB->id)->count())->toBe(1);
    expect(UserNotification::where('user_id', $captainA->id)->first()->message)
        ->toBe(__('app.player_left_notification', ['player' => $player->name, 'team' => $teamA->name]));
    expect(UserNotification::where('user_id', $coachA->id)->count())->toBe(1);
    expect(UserNotification::count())->toBe(5);
});

test('editing a player without changing the team does not notify', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $playerUser = User::factory()->create();
    $captain = User::factory()->create();
    $team = Team::factory()->create(['captain_id' => $captain->id]);
    $player = Player::factory()->create(['user_id' => $playerUser->id, 'team_id' => $team->id]);

    Livewire::actingAs($admin)
        ->test(EditPlayerPage::class, ['player' => $player])
        ->set('number', 7)
        ->call('update')
        ->assertHasNoErrors();

    expect(UserNotification::count())->toBe(0);
});
