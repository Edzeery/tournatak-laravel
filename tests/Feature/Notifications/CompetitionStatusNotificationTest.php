<?php

use App\Livewire\Admin\Competitions\CompetitionsPage;
use App\Models\Competition;
use App\Models\CompetitionDomain;
use App\Models\CompetitionSubtype;
use App\Models\CompetitionType;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\CompetitionService;
use Livewire\Livewire;

test('approving a competition notifies the organizer', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $organizer = User::factory()->create();
    $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);

    Livewire::actingAs($admin)->test(CompetitionsPage::class)
        ->call('approve', $competition->id);

    expect($competition->refresh()->approval_status)->toBe('approved');
    $this->assertDatabaseHas('notifications', [
        'user_id' => $organizer->id,
        'title' => __('app.competition_approved_notification'),
        'message' => $competition->name,
        'link' => route('admin.competitions.index'),
    ]);
    expect(UserNotification::where('user_id', $admin->id)->count())->toBe(0);
});

test('rejecting a competition notifies the organizer', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $organizer = User::factory()->create();
    $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);

    Livewire::actingAs($admin)->test(CompetitionsPage::class)
        ->call('reject', $competition->id);

    expect($competition->refresh()->approval_status)->toBe('rejected');
    $this->assertDatabaseHas('notifications', [
        'user_id' => $organizer->id,
        'title' => __('app.competition_rejected_notification'),
        'message' => $competition->name,
        'link' => route('admin.competitions.index'),
    ]);
    expect(UserNotification::where('user_id', $admin->id)->count())->toBe(0);
});

test('creating an official competition notifies all admins for review', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $organizer = User::factory()->create();
    $this->actingAs($organizer);

    $domain = CompetitionDomain::factory()->submission()->create();
    $type = CompetitionType::factory()->create();
    $subtype = CompetitionSubtype::factory()->create();

    $competition = app(CompetitionService::class)->create([
        'name' => 'Official Championship',
        'domain_id' => $domain->id,
        'type_id' => $type->id,
        'subtype_id' => $subtype->id,
    ]);

    expect($competition->approval_status)->toBe('pending');
    $this->assertDatabaseHas('notifications', [
        'user_id' => $admin->id,
        'title' => __('app.competition_submitted_title'),
        'message' => __('app.competition_submitted_notification', ['competition' => $competition->name]),
        'link' => route('admin.competitions.index'),
    ]);
    expect(UserNotification::where('user_id', $organizer->id)->count())->toBe(0);
});

test('creating a casual competition does not notify admins', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $organizer = User::factory()->create();
    $this->actingAs($organizer);

    $domain = CompetitionDomain::factory()->submission()->create();
    $type = CompetitionType::factory()->create();
    $subtype = CompetitionSubtype::factory()->create();

    $competition = app(CompetitionService::class)->create([
        'name' => 'Casual Meetup',
        'competition_profile' => Competition::PROFILE_CASUAL,
        'domain_id' => $domain->id,
        'type_id' => $type->id,
        'subtype_id' => $subtype->id,
    ]);

    expect($competition->approval_status)->toBe('approved');
    expect(UserNotification::count())->toBe(0);
});

test('approving a competition via the service notifies the organizer once', function () {
    $organizer = User::factory()->create();
    $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);

    app(CompetitionService::class)->approve($competition);

    expect(UserNotification::count())->toBe(1);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $organizer->id,
        'title' => __('app.competition_approved_notification'),
        'link' => route('admin.competitions.index'),
    ]);
});
