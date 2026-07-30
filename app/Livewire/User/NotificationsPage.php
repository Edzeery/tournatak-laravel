<?php

namespace App\Livewire\User;

use App\Models\UserNotification;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class NotificationsPage extends Component
{
    use WithPagination;

    public string $filter = 'all';

    public function markAsRead(int $id): void
    {
        app(NotificationService::class)->markAsRead(auth()->id(), $id);
    }

    public function markAllRead(): void
    {
        app(NotificationService::class)->markAllRead(auth()->id());
    }

    public function deleteNotification(int $id): void
    {
        app(NotificationService::class)->delete(auth()->id(), $id);
    }

    public function render()
    {
        $query = UserNotification::where('user_id', auth()->id());

        if ($this->filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($this->filter === 'read') {
            $query->where('is_read', true);
        }

        return view('livewire.user.notifications-page', [
            'notifications' => $query->latest()->paginate(15),
            'unreadCount' => UserNotification::where('user_id', auth()->id())->where('is_read', false)->count(),
            'title' => __('app.notifications'),
        ]);
    }
}
