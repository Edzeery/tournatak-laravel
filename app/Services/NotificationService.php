<?php

namespace App\Services;

use App\Models\UserNotification;

class NotificationService
{
    public function create(
        int $userId,
        string $title,
        ?string $message = null,
        ?string $icon = null,
        ?string $link = null,
        ?string $type = 'info'
    ): UserNotification {
        return UserNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'icon' => $icon ?? $this->defaultIcon($type),
            'link' => $link,
            'is_read' => false,
        ]);
    }

    public function createForAdmins(
        string $title,
        ?string $message = null,
        ?string $icon = null,
        ?string $link = null,
        ?string $type = 'info'
    ): void {
        $adminUserIds = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->pluck('id');
        foreach ($adminUserIds as $userId) {
            $this->create($userId, $title, $message, $icon, $link, $type);
        }
    }

    public function markAsRead(int $userId, int $notificationId): ?UserNotification
    {
        $notification = UserNotification::where('user_id', $userId)->find($notificationId);

        if ($notification && !$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        return $notification;
    }

    public function markAllRead(int $userId): int
    {
        return UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function delete(int $userId, int $notificationId): bool
    {
        $notification = UserNotification::where('user_id', $userId)->find($notificationId);

        if ($notification) {
            return $notification->delete();
        }

        return false;
    }

    public function getUnreadCount(int $userId): int
    {
        return UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function getUserNotifications(int $userId, string $filter = 'all')
    {
        $query = UserNotification::where('user_id', $userId);

        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        return $query->latest()->paginate(20);
    }

    private function defaultIcon(string $type): string
    {
        return match ($type) {
            'success' => 'bi-check-circle-fill text-success',
            'warning' => 'bi-exclamation-triangle-fill text-warning',
            'danger' => 'bi-x-circle-fill text-danger',
            'info' => 'bi-info-circle-fill text-info',
            default => 'bi-bell-fill text-primary',
        };
    }
}
