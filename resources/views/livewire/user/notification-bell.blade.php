<div>
@if(auth()->check())
<div class="nav-item" wire:click="toggle" x-data="{ open: @entangle('open') }" @click.outside="open = false" style="position:relative;">
    <button class="nav-link position-relative" style="background:none;border:none;cursor:pointer;padding:0.5rem;">
        <i class="bi bi-bell" style="font-size:1.1rem;"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background:#ef4444;font-size:0.65rem;padding:3px 6px;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    @if($open)
    <div class="border-0 shadow-lg show" wire:click.away="open = false"
        style="border-radius:14px;min-width:360px;max-width:400px;position:absolute;top:100%;{{ isRtl() ? 'left:0' : 'right:0' }};z-index:1050;background:#1a1f35;overflow:hidden;">
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom" style="border-color:rgba(255,255,255,0.06) !important;">
            <span class="fw-bold text-white" style="font-size:0.95rem;">
                <i class="bi bi-bell-fill text-gold me-2"></i> {{ __('app.notifications') }}
            </span>
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="btn btn-sm" style="color:var(--primary);background:none;border:none;font-size:0.8rem;font-weight:600;">
                    {{ __('app.mark_all_read') }}
                </button>
            @endif
        </div>

        <div style="max-height:380px;overflow-y:auto;">
            @forelse($notifications as $notification)
                <a href="{{ $notification['link'] ?? '#' }}" wire:click="markAsRead({{ $notification['id'] }})" class="d-flex gap-3 px-3 py-3 text-decoration-none border-bottom" style="border-color:rgba(255,255,255,0.04) !important;{{ !$notification['is_read'] ? 'background:rgba(255,193,7,0.04);' : '' }}">
                    <div class="flex-shrink-0 mt-1">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:rgba(255,193,7,0.1);color:var(--primary);">
                            <i class="bi bi-{{ $notification['icon'] ?? 'bell' }}" style="font-size:1rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="fw-bold" style="font-size:0.85rem;color:{{ !$notification['is_read'] ? '#fff' : 'rgba(255,255,255,0.7)' }};">{{ $notification['title'] }}</div>
                        <div style="font-size:0.8rem;color:rgba(255,255,255,0.4);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $notification['message'] }}</div>
                        <div style="font-size:0.72rem;color:rgba(255,255,255,0.25);margin-top:2px;">{{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}</div>
                    </div>
                    @if(!$notification['is_read'])
                        <div class="flex-shrink-0 mt-2">
                            <div class="rounded-circle" style="width:8px;height:8px;background:var(--primary);"></div>
                        </div>
                    @endif
                </a>
            @empty
                <div class="text-center py-4">
                    <i class="bi bi-bell-slash" style="font-size:2rem;color:rgba(255,255,255,0.15);"></i>
                    <div class="mt-2" style="color:rgba(255,255,255,0.3);font-size:0.85rem;">{{ __('app.no_notifications') }}</div>
                </div>
            @endforelse
        </div>

        @if(count($notifications) > 0)
        <div class="px-3 py-2 border-top text-center" style="border-color:rgba(255,255,255,0.06) !important;">
            <a href="{{ route('user.notifications') }}" style="color:var(--primary);font-size:0.85rem;font-weight:600;text-decoration:none;">
                {{ __('app.view_all_notifications') }} <i class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }} ms-1"></i>
            </a>
        </div>
        @endif
    </div>
    @endif
</div>
@endif
</div>
