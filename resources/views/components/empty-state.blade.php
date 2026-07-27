@props([
    'icon' => 'bi-inbox',
    'title' => null,
    'message' => null,
    'actionLabel' => null,
    'actionUrl' => null,
])

<div class="empty-state text-center py-5 px-3">
    <div class="empty-state-icon mb-3">
        <i class="bi {{ $icon }}"></i>
    </div>
    @if($title)
        <h5 class="text-white fw-bold mb-2">{{ $title }}</h5>
    @endif
    @if($message)
        <p class="mb-0" style="color:rgba(255,255,255,0.45);max-width:400px;margin:0 auto;">
            {{ $message }}
        </p>
    @endif
    @if($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn btn-gold mt-3">
            <i class="bi bi-plus-circle me-1"></i> {{ $actionLabel }}
        </a>
    @endif
    @if($slot)
        <div class="mt-3">{{ $slot }}</div>
    @endif
</div>
