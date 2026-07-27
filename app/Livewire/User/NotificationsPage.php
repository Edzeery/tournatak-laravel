<?php

namespace App\Livewire\User;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UserNotification;

#[Layout('layouts.app')]
class NotificationsPage extends Component
{
    use WithPagination;

    public string $filter = 'all';

    public function markAsRead(int $id): void
    {
        $notification = UserNotification::where('user_id', auth()->id())->find($id);
        if ($notification && !$notification->is_read) {
            $notification->update(['is_read' => true]);
        }
    }

    public function markAllRead(): void
    {
        UserNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function deleteNotification(int $id): void
    {
        UserNotification::where('user_id', auth()->id())->find($id)?->delete();
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
