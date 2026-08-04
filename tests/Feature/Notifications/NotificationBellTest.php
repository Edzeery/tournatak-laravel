<?php

use App\Livewire\User\NotificationBell;
use App\Models\User;
use App\Models\UserNotification;
use Livewire\Livewire;

test('bell shows unread count and notifications for the authenticated user only', function () {
    $user = User::factory()->create();
    UserNotification::create(['user_id' => $user->id, 'title' => 'one']);
    UserNotification::create(['user_id' => $user->id, 'title' => 'two']);
    UserNotification::create(['user_id' => $user->id, 'title' => 'three', 'is_read' => true]);
    $other = User::factory()->create();
    UserNotification::create(['user_id' => $other->id, 'title' => 'other']);

    Livewire::actingAs($user)
        ->test(NotificationBell::class)
        ->assertSet('unreadCount', 2)
        ->assertCount('notifications', 3);
});

test('bell lists only the latest 8 notifications', function () {
    $user = User::factory()->create();
    for ($i = 0; $i < 10; $i++) {
        UserNotification::create(['user_id' => $user->id, 'title' => "n{$i}"]);
    }

    Livewire::actingAs($user)
        ->test(NotificationBell::class)
        ->assertCount('notifications', 8);
});

test('bell markAsRead updates the unread count', function () {
    $user = User::factory()->create();
    $a = UserNotification::create(['user_id' => $user->id, 'title' => 'a']);
    UserNotification::create(['user_id' => $user->id, 'title' => 'b']);

    Livewire::actingAs($user)
        ->test(NotificationBell::class)
        ->assertSet('unreadCount', 2)
        ->call('markAsRead', $a->id)
        ->assertSet('unreadCount', 1);
});

test('bell cannot mark another users notification as read', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $otherN = UserNotification::create(['user_id' => $other->id, 'title' => 'b']);

    Livewire::actingAs($user)
        ->test(NotificationBell::class)
        ->call('markAsRead', $otherN->id)
        ->assertSet('unreadCount', 0);

    $this->assertDatabaseHas('notifications', ['id' => $otherN->id, 'is_read' => false]);
});

test('bell markAllRead clears the unread count', function () {
    $user = User::factory()->create();
    UserNotification::create(['user_id' => $user->id, 'title' => 'a']);
    UserNotification::create(['user_id' => $user->id, 'title' => 'b']);

    Livewire::actingAs($user)
        ->test(NotificationBell::class)
        ->assertSet('unreadCount', 2)
        ->call('markAllRead')
        ->assertSet('unreadCount', 0);
});
