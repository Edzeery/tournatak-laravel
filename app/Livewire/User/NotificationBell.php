<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\UserNotification;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public array $notifications = [];
    public bool $open = false;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        if (!auth()->check()) return;

        $this->unreadCount = UserNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        $this->notifications = UserNotification::where('user_id', auth()->id())
            ->latest()
            ->limit(8)
            ->get()
            ->toArray();
    }

    public function toggle()
    {
        $this->open = !$this->open;
        if ($this->open) {
            $this->loadData();
        }
    }

    public function markAsRead(int $id)
    {
        $notification = UserNotification::where('user_id', auth()->id())->find($id);
        if ($notification && !$notification->is_read) {
            $notification->update(['is_read' => true]);
            $this->loadData();
        }
    }

    public function markAllRead()
    {
        UserNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        $this->loadData();
    }

    public function render()
    {
        $this->loadData();
        return view('livewire.user.notification-bell');
    }
}
