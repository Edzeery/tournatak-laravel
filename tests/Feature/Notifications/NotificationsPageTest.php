<?php

use App\Livewire\User\NotificationsPage;
use App\Models\User;
use App\Models\UserNotification;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guest is redirected from the notifications page', function () {
    $this->get('/user/notifications')
        ->assertRedirect('/login');
});

test('notifications page renders for the user', function () {
    $this->actingAs($this->user)
        ->get('/user/notifications')
        ->assertOk();
});

test('page lists paginated notifications', function () {
    for ($i = 0; $i < 20; $i++) {
        UserNotification::create(['user_id' => $this->user->id, 'title' => "n{$i}"]);
    }

    Livewire::actingAs($this->user)
        ->test(NotificationsPage::class)
        ->assertViewHas('notifications', fn ($paginator) => $paginator->total() === 20 && $paginator->count() === 15);
});

test('page filters by unread and read', function () {
    UserNotification::create(['user_id' => $this->user->id, 'title' => 'unread1']);
    UserNotification::create(['user_id' => $this->user->id, 'title' => 'read1', 'is_read' => true]);

    Livewire::actingAs($this->user)
        ->test(NotificationsPage::class)
        ->set('filter', 'unread')
        ->assertViewHas('notifications', fn ($p) => $p->total() === 1 && $p->first()->title === 'unread1');

    Livewire::actingAs($this->user)
        ->test(NotificationsPage::class)
        ->set('filter', 'read')
        ->assertViewHas('notifications', fn ($p) => $p->total() === 1 && $p->first()->title === 'read1');
});

test('page does not list another users notifications', function () {
    $other = User::factory()->create();
    UserNotification::create(['user_id' => $other->id, 'title' => 'secret']);

    Livewire::actingAs($this->user)
        ->test(NotificationsPage::class)
        ->assertViewHas('notifications', fn ($p) => $p->total() === 0);
});

test('markAsRead updates the unread count and persists', function () {
    $a = UserNotification::create(['user_id' => $this->user->id, 'title' => 'a']);
    UserNotification::create(['user_id' => $this->user->id, 'title' => 'b']);

    Livewire::actingAs($this->user)
        ->test(NotificationsPage::class)
        ->call('markAsRead', $a->id)
        ->assertViewHas('unreadCount', 1);

    $this->assertDatabaseHas('notifications', ['id' => $a->id, 'is_read' => true]);
});

test('markAllRead clears the unread count', function () {
    UserNotification::create(['user_id' => $this->user->id, 'title' => 'a']);
    UserNotification::create(['user_id' => $this->user->id, 'title' => 'b']);

    Livewire::actingAs($this->user)
        ->test(NotificationsPage::class)
        ->call('markAllRead')
        ->assertViewHas('unreadCount', 0);
});

test('deleteNotification cannot remove another users notification', function () {
    $own = UserNotification::create(['user_id' => $this->user->id, 'title' => 'a']);
    $other = User::factory()->create();
    $otherN = UserNotification::create(['user_id' => $other->id, 'title' => 'b']);

    Livewire::actingAs($this->user)
        ->test(NotificationsPage::class)
        ->call('deleteNotification', $otherN->id);

    $this->assertDatabaseHas('notifications', ['id' => $otherN->id]);
    $this->assertDatabaseHas('notifications', ['id' => $own->id]);
});
