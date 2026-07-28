<div class="  w-4xl">
    @if (auth()->check())
        <div class="nav-item" x-data="notificationBell()" @click.outside="close()" @keydown.escape.window="close()"
             class="position-relative">
            <button class="nav-link notification-bell-btn" @click="toggle()" aria-label="{{ __('app.notifications') }}">
                <i class="bi bi-bell"></i>
                @if ($unreadCount > 0)
                    <span class="notification-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </button>

            <div x-show="open" x-transition:enter="notif-enter" x-transition:enter-start="notif-enter-start"
                x-transition:enter-end="notif-enter-end" x-transition:leave="notif-leave"
                x-transition:leave-start="notif-leave-start" x-transition:leave-end="notif-leave-end" x-cloak
                class="notification-popup" @click.outside="close()">
                <div class="notif-header">
                    <span class="fw-bold fs-md">
                        <i class="bi bi-bell-fill text-gold me-2"></i> {{ __('app.notifications') }}
                    </span>
                    @if ($unreadCount > 0)
                        <button wire:click="markAllRead" class="notif-mark-read-btn" @click="close()">
                            {{ __('app.mark_all_read') }}
                        </button>
                    @endif
                </div>

                <div class="notif-list">
                    @forelse($notifications as $notification)
                        <a href="{{ $notification['link'] ?? '#' }}" wire:click="markAsRead({{ $notification['id'] }})"
                            class="notif-item {{ !$notification['is_read'] ? 'notif-item-unread' : '' }}">
                            <div class="notif-icon-wrap">
                                <div class="notif-icon">
                                    <i class="bi bi-{{ $notification['icon'] ?? 'bell' }}"></i>
                                </div>
                            </div>
                            <div class="notif-content">
                                <div class="notif-title {{ !$notification['is_read'] ? 'fw-bold' : '' }}">
                                    {{ $notification['title'] }}</div>
                                <div class="notif-message">{{ $notification['message'] }}</div>
                                <div class="notif-time">
                                    {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}</div>
                            </div>
                            @if (!$notification['is_read'])
                                <div class="notif-dot"></div>
                            @endif
                        </a>
                    @empty
                        <div class="notif-empty">
                            <i class="bi bi-bell-slash"></i>
                            <div>{{ __('app.no_notifications') }}</div>
                        </div>
                    @endforelse
                </div>

                @if (count($notifications) > 0)
                    <div class="notif-footer">
                        <a href="{{ route('user.notifications') }}" class="notif-view-all">
                            {{ __('app.view_all_notifications') }} <i
                                class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }} ms-1"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        function notificationBell() {
            return {
                open: false,
                toggle() {
                    this.open = !this.open;
                },
                close() {
                    this.open = false;
                }
            }
        }
    </script>
@endpush
