<div class="container py-4 container-page-sm">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="text-white fw-bold mb-0">
            <i class="bi bi-bell-fill text-gold me-2"></i>{{ __('app.notifications') }}
        </h4>
        @if($unreadCount > 0)
            <button wire:click="markAllRead" class="btn btn-sm btn-gold-outline">
                <i class="bi bi-check-all me-1"></i>{{ __('app.mark_all_read') }}
            </button>
        @endif
    </div>

    <div class="d-flex gap-2 mb-4">
        <button wire:click="$set('filter', 'all')" class="btn btn-sm {{ $filter === 'all' ? 'notif-filter-active' : 'notif-filter btn-outline-secondary' }}">
            {{ __('app.all') }}
        </button>
        <button wire:click="$set('filter', 'unread')" class="btn btn-sm {{ $filter === 'unread' ? 'notif-filter-active' : 'notif-filter btn-outline-secondary' }}">
            {{ __('app.unread') }} @if($unreadCount > 0)<span class="badge notif-badge-count">{{ $unreadCount }}</span>@endif
        </button>
        <button wire:click="$set('filter', 'read')" class="btn btn-sm {{ $filter === 'read' ? 'notif-filter-active' : 'notif-filter btn-outline-secondary' }}">
            {{ __('app.read') }}
        </button>
    </div>

    @if($notifications->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-bell-slash fs-3xl text-theme-muted"></i>
            <p class="mt-3 text-theme-muted">{{ __('app.no_notifications') }}</p>
        </div>
    @else
        <div class="d-flex flex-column gap-2">
            @foreach($notifications as $notification)
                <div class="d-flex align-items-start gap-3 p-3 notif-page-item {{ !$notification->is_read ? 'notif-page-item-unread' : 'notif-page-item-read' }}" wire:click="markAsRead({{ $notification->id }})">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center w-44 h-44 notif-page-icon">
                            <i class="bi bi-{{ $notification->icon ?? 'bell' }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold fs-base {{ !$notification->is_read ? 'notif-page-title-unread' : 'notif-page-title-read' }}">{{ $notification->title }}</span>
                            @if(!$notification->is_read)
                                <span class="rounded-circle notif-page-dot"></span>
                            @endif
                        </div>
                        @if($notification->message)
                            <div class="fs-base text-theme-muted notif-page-msg">{{ $notification->message }}</div>
                        @endif
                        <div class="fs-sm text-theme-muted notif-page-time">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @if($notification->link)
                        <a href="{{ $notification->link }}" class="flex-shrink-0 text-gold fs-xs" onclick="event.stopPropagation();">
                            <i class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }}"></i>
                        </a>
                    @endif
                    <button class="flex-shrink-0 btn p-0 text-theme-muted notif-page-del" wire:click.stop="deleteNotification({{ $notification->id }})" onclick="event.stopPropagation();">
                        <i class="bi bi-x-lg fs-xs"></i>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
