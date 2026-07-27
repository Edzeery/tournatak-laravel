<div class="container py-4" style="max-width:800px;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="text-white fw-bold mb-0">
            <i class="bi bi-bell-fill text-gold me-2"></i>{{ __('app.notifications') }}
        </h4>
        @if($unreadCount > 0)
            <button wire:click="markAllRead" class="btn btn-sm" style="color:var(--primary);border:1px solid rgba(255,193,7,0.3);border-radius:8px;">
                <i class="bi bi-check-all me-1"></i>{{ __('app.mark_all_read') }}
            </button>
        @endif
    </div>

    <div class="d-flex gap-2 mb-4">
        <button wire:click="$set('filter', 'all')" class="btn btn-sm {{ $filter === 'all' ? '' : 'btn-outline-secondary' }}" style="{{ $filter === 'all' ? 'background:var(--primary);color:#000;font-weight:600;border-radius:8px;' : 'border-radius:8px;color:rgba(255,255,255,0.6);border-color:rgba(255,255,255,0.1);' }}">
            {{ __('app.all') }}
        </button>
        <button wire:click="$set('filter', 'unread')" class="btn btn-sm {{ $filter === 'unread' ? '' : 'btn-outline-secondary' }}" style="{{ $filter === 'unread' ? 'background:var(--primary);color:#000;font-weight:600;border-radius:8px;' : 'border-radius:8px;color:rgba(255,255,255,0.6);border-color:rgba(255,255,255,0.1);' }}">
            {{ __('app.unread') }} @if($unreadCount > 0)<span class="badge" style="background:rgba(255,193,7,0.2);color:var(--primary);">{{ $unreadCount }}</span>@endif
        </button>
        <button wire:click="$set('filter', 'read')" class="btn btn-sm {{ $filter === 'read' ? '' : 'btn-outline-secondary' }}" style="{{ $filter === 'read' ? 'background:var(--primary);color:#000;font-weight:600;border-radius:8px;' : 'border-radius:8px;color:rgba(255,255,255,0.6);border-color:rgba(255,255,255,0.1);' }}">
            {{ __('app.read') }}
        </button>
    </div>

    @if($notifications->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-bell-slash" style="font-size:3rem;color:rgba(255,255,255,0.1);"></i>
            <p class="mt-3" style="color:rgba(255,255,255,0.4);">{{ __('app.no_notifications') }}</p>
        </div>
    @else
        <div class="d-flex flex-column gap-2">
            @foreach($notifications as $notification)
                <div class="d-flex align-items-start gap-3 p-3" wire:click="markAsRead({{ $notification->id }})" style="background:{{ !$notification->is_read ? 'rgba(255,193,7,0.04)' : 'rgba(255,255,255,0.02)' }};border:1px solid rgba(255,255,255,0.05);border-radius:12px;cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='{{ !$notification->is_read ? 'rgba(255,193,7,0.04)' : 'rgba(255,255,255,0.02)' }}'">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:rgba(255,193,7,0.1);color:var(--primary);">
                            <i class="bi bi-{{ $notification->icon ?? 'bell' }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold" style="font-size:0.9rem;color:{{ !$notification->is_read ? '#fff' : 'rgba(255,255,255,0.7)' }};">{{ $notification->title }}</span>
                            @if(!$notification->is_read)
                                <span class="rounded-circle" style="width:8px;height:8px;background:var(--primary);flex-shrink:0;"></span>
                            @endif
                        </div>
                        @if($notification->message)
                            <div style="font-size:0.85rem;color:rgba(255,255,255,0.45);margin-top:2px;">{{ $notification->message }}</div>
                        @endif
                        <div style="font-size:0.75rem;color:rgba(255,255,255,0.25);margin-top:4px;">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @if($notification->link)
                        <a href="{{ $notification->link }}" class="flex-shrink-0" style="color:var(--primary);font-size:0.8rem;" onclick="event.stopPropagation();">
                            <i class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }}"></i>
                        </a>
                    @endif
                    <button class="flex-shrink-0 btn p-0" style="color:rgba(255,255,255,0.2);background:none;border:none;" wire:click.stop="deleteNotification({{ $notification->id }})" onclick="event.stopPropagation();">
                        <i class="bi bi-x-lg" style="font-size:0.8rem;"></i>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
