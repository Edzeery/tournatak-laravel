<?php

use App\Livewire\Admin\Matches\CreateMatchPage;
use App\Livewire\Admin\Matches\MatchControlPage;
use App\Livewire\Admin\Matches\MatchesPage;
use App\Models\Competition;
use App\Models\Match_;
use App\Models\Registration;
use App\Models\Team;
use App\Models\TeamStaff;
use App\Models\User;
use App\Models\UserNotification;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->captain1 = User::factory()->create();
    $this->captain2 = User::factory()->create();
    $this->coach = User::factory()->create();
    $this->registrant = User::factory()->create();
    $this->unrelated = User::factory()->create();

    $this->competition = Competition::factory()->create();
    $this->team1 = Team::factory()->create(['captain_id' => $this->captain1->id]);
    $this->team2 = Team::factory()->create(['captain_id' => $this->captain2->id]);
    TeamStaff::create([
        'team_id' => $this->team1->id,
        'user_id' => $this->coach->id,
        'staff_role' => 'head_coach',
        'is_active' => true,
    ]);
    Registration::factory()->individual()->create([
        'competition_id' => $this->competition->id,
        'user_id' => $this->registrant->id,
    ]);

    $this->match = Match_::factory()->create([
        'competition_id' => $this->competition->id,
        'team1_id' => $this->team1->id,
        'team2_id' => $this->team2->id,
        'status' => 'scheduled',
    ]);
});

test('starting a match notifies both captains, the active staff, and individual registrants', function () {
    Livewire::actingAs($this->admin)->test(MatchesPage::class)
        ->call('startMatch', $this->match->id);

    $label = "{$this->team1->name} vs {$this->team2->name}";
    $link = route('matches.live', ['match' => $this->match->id]);

    foreach ([$this->captain1, $this->captain2, $this->coach, $this->registrant] as $user) {
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'title' => __('app.match_started_title'),
            'message' => __('app.match_started_notification', ['match' => $label]),
            'link' => $link,
        ]);
    }

    expect(UserNotification::count())->toBe(4);
    expect(UserNotification::where('user_id', $this->unrelated->id)->count())->toBe(0);
});

test('creating a match notifies both captains, the active staff, and individual registrants', function () {
    Livewire::actingAs($this->admin)->test(CreateMatchPage::class)
        ->set('competition_id', $this->competition->id)
        ->set('team1_id', $this->team1->id)
        ->set('team2_id', $this->team2->id)
        ->set('status', 'scheduled')
        ->call('store')
        ->assertHasNoErrors();

    $link = route('matches.index');

    foreach ([$this->captain1, $this->captain2, $this->coach, $this->registrant] as $user) {
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'title' => __('app.match_scheduled_title'),
            'message' => __('app.match_scheduled_notification', [
                'match' => "{$this->team1->name} vs {$this->team2->name}",
                'competition' => $this->competition->name,
            ]),
            'link' => $link,
        ]);
    }

    expect(UserNotification::count())->toBe(4);
    expect(UserNotification::where('user_id', $this->unrelated->id)->count())->toBe(0);
});

test('starting a match notifies each unique participant only once', function () {
    TeamStaff::create([
        'team_id' => $this->team2->id,
        'user_id' => $this->registrant->id,
        'staff_role' => 'assistant_coach',
        'is_active' => true,
    ]);

    Livewire::actingAs($this->admin)->test(MatchesPage::class)
        ->call('startMatch', $this->match->id);

    expect(UserNotification::where('user_id', $this->registrant->id)->count())->toBe(1);
    expect(UserNotification::count())->toBe(4);
});

test('ending a match notifies participants with the result and a competition link', function () {
    Livewire::actingAs($this->admin)->test(MatchesPage::class)
        ->call('endMatch', $this->match->id);

    $label = "{$this->team1->name} vs {$this->team2->name}";
    $link = route('competitions.show', ['competition' => $this->competition->id]);

    foreach ([$this->captain1, $this->captain2, $this->coach, $this->registrant] as $user) {
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'title' => __('app.match_completed'),
            'message' => __('app.match_completed_notification', ['match' => $label, 'competition' => $this->competition->name]),
            'link' => $link,
        ]);
    }

    $participantIds = [$this->captain1->id, $this->captain2->id, $this->coach->id, $this->registrant->id];
    expect(UserNotification::whereIn('user_id', $participantIds)->where('title', __('app.match_completed'))->count())->toBe(4);
});

test('completing a match from the control page (service transition) notifies participants', function () {
    $this->match->update(['status' => 'in_progress']);

    Livewire::actingAs($this->admin)->test(MatchControlPage::class, ['match' => $this->match])
        ->call('endMatch');

    $participantIds = [$this->captain1->id, $this->captain2->id, $this->coach->id, $this->registrant->id];
    expect(UserNotification::whereIn('user_id', $participantIds)->where('title', __('app.match_completed'))->count())->toBe(4);
});

test('a goal scored notifies participants', function () {
    $this->match->update(['status' => 'in_progress']);

    Livewire::actingAs($this->admin)->test(MatchControlPage::class, ['match' => $this->match])
        ->call('quickGoal', $this->team1->id);

    $participantIds = [$this->captain1->id, $this->captain2->id, $this->coach->id, $this->registrant->id];
    expect(UserNotification::whereIn('user_id', $participantIds)->where('title', __('app.goal_scored'))->count())->toBe(4);
});

test('starting a match with no reachable participants creates no notifications', function () {
    UserNotification::truncate();

    $captainless = Team::factory()->create(['captain_id' => null]);
    $match = Match_::factory()->create([
        'competition_id' => Competition::factory(),
        'team1_id' => $captainless->id,
        'team2_id' => $captainless->id,
        'status' => 'scheduled',
    ]);

    Livewire::actingAs($this->admin)->test(MatchesPage::class)
        ->call('startMatch', $match->id);

    expect(UserNotification::count())->toBe(0);
});
