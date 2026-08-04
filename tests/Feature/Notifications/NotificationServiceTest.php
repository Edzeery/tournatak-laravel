<?php

use App\Models\User;
use App\Models\UserNotification;
use App\Services\NotificationService;

beforeEach(function () {
    $this->service = app(NotificationService::class);
});

test('create stores a notification for the given user', function () {
    $user = User::factory()->create();

    $notification = $this->service->create($user->id, 'Hello', 'World', 'bi-bell', '/home');

    expect($notification)->toBeInstanceOf(UserNotification::class);
    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
        'user_id' => $user->id,
        'title' => 'Hello',
        'message' => 'World',
        'icon' => 'bi-bell',
        'link' => '/home',
        'is_read' => false,
    ]);
});

test('create derives the default icon from the type', function () {
    $user = User::factory()->create();

    $notification = $this->service->create($user->id, 'Warning', null, null, null, 'warning');

    expect($notification->icon)->toBe('bi-exclamation-triangle-fill text-warning');
});

test('notifyUser is a thin alias over create', function () {
    $user = User::factory()->create();

    $notification = $this->service->notifyUser($user, 'Hello', 'Body', 'bi-bell', '/user/registrations', 'info');

    expect($notification)->toBeInstanceOf(UserNotification::class);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $user->id,
        'title' => 'Hello',
        'message' => 'Body',
        'link' => '/user/registrations',
    ]);
});

test('createForRole notifies only users with that role', function () {
    User::factory()->create()->assignRole('admin');
    $organizer = User::factory()->create()->assignRole('organizer');
    User::factory()->create();

    $this->service->createForRole('organizer', 'Only organizers');

    expect(UserNotification::count())->toBe(1);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $organizer->id,
        'title' => 'Only organizers',
    ]);
});

test('createForAdmins notifies all admins and is backward compatible', function () {
    $admin1 = User::factory()->create()->assignRole('admin');
    $admin2 = User::factory()->create()->assignRole('admin');
    User::factory()->create()->assignRole('organizer');
    User::factory()->create();

    $this->service->createForAdmins('Admin alert', 'Body', 'bi-bell', '/panel/matches', 'success');

    expect(UserNotification::count())->toBe(2);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $admin1->id,
        'title' => 'Admin alert',
        'message' => 'Body',
        'link' => '/panel/matches',
    ]);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $admin2->id,
        'title' => 'Admin alert',
        'message' => 'Body',
        'link' => '/panel/matches',
    ]);
});

test('createForAdmins output matches createForRole with the admin role', function () {
    $admin = User::factory()->create()->assignRole('admin');

    $this->service->createForAdmins('Same', 'Message', 'bi-icon', '/link', 'success');

    $this->assertDatabaseHas('notifications', [
        'user_id' => $admin->id,
        'title' => 'Same',
        'message' => 'Message',
        'icon' => 'bi-icon',
        'link' => '/link',
    ]);
});

test('markAsRead flips the owners unread notification', function () {
    $user = User::factory()->create();
    $notification = UserNotification::create(['user_id' => $user->id, 'title' => 'T']);

    $result = $this->service->markAsRead($user->id, $notification->id);

    expect($result)->not->toBeNull();
    expect($result->is_read)->toBeTrue();
    $this->assertDatabaseHas('notifications', ['id' => $notification->id, 'is_read' => true]);
});

test('markAsRead cannot affect another users notification', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $notification = UserNotification::create(['user_id' => $owner->id, 'title' => 'T']);

    $result = $this->service->markAsRead($other->id, $notification->id);

    expect($result)->toBeNull();
    $this->assertDatabaseHas('notifications', ['id' => $notification->id, 'is_read' => false]);
});

test('markAllRead marks only the current users unread notifications', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    UserNotification::create(['user_id' => $user->id, 'title' => 'a', 'is_read' => false]);
    UserNotification::create(['user_id' => $user->id, 'title' => 'b', 'is_read' => true]);
    UserNotification::create(['user_id' => $other->id, 'title' => 'c', 'is_read' => false]);

    $count = $this->service->markAllRead($user->id);

    expect($count)->toBe(1);
    $this->assertDatabaseHas('notifications', ['user_id' => $other->id, 'is_read' => false]);
});

test('delete removes only the users own notification', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $own = UserNotification::create(['user_id' => $user->id, 'title' => 'a']);
    $otherN = UserNotification::create(['user_id' => $other->id, 'title' => 'b']);

    $result = $this->service->delete($user->id, $own->id);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('notifications', ['id' => $own->id]);
    $this->assertDatabaseHas('notifications', ['id' => $otherN->id]);
});

test('delete returns false for another users notification', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $otherN = UserNotification::create(['user_id' => $other->id, 'title' => 'b']);

    $result = $this->service->delete($user->id, $otherN->id);

    expect($result)->toBeFalse();
    $this->assertDatabaseHas('notifications', ['id' => $otherN->id]);
});

test('getUnreadCount counts only that users unread notifications', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    UserNotification::create(['user_id' => $user->id, 'title' => 'a']);
    UserNotification::create(['user_id' => $user->id, 'title' => 'b', 'is_read' => true]);
    UserNotification::create(['user_id' => $other->id, 'title' => 'c']);

    expect($this->service->getUnreadCount($user->id))->toBe(1);
    expect($this->service->getUnreadCount($other->id))->toBe(1);
});

test('getUserNotifications filters all unread and read per user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $unread = UserNotification::create(['user_id' => $user->id, 'title' => 'a']);
    UserNotification::create(['user_id' => $user->id, 'title' => 'b', 'is_read' => true]);
    UserNotification::create(['user_id' => $other->id, 'title' => 'c']);

    expect($this->service->getUserNotifications($user->id, 'all')->total())->toBe(2);
    expect($this->service->getUserNotifications($user->id, 'unread')->total())->toBe(1);
    expect($this->service->getUserNotifications($user->id, 'read')->total())->toBe(1);
    expect($this->service->getUserNotifications($user->id, 'unread')->first()->id)->toBe($unread->id);
});
