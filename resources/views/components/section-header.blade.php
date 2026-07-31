@props([
    'title' => '',
    'icon' => null,
    'subtitle' => null,
    'breadcrumbs' => [],
    'noMargin' => false,
])

<div {{ $attributes->merge(['class' => $noMargin ? '' : 'mb-4']) }}>
    @if($breadcrumbs)
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb fs-base">
                @foreach($breadcrumbs as $crumb)
                    @isset($crumb['route'])
                        <li class="breadcrumb-item">
                            <a href="{{ $crumb['route'] }}" class="breadcrumb-link">{{ $crumb['label'] }}</a>
                        </li>
                    @else
                        <li class="breadcrumb-item active">{{ $crumb['label'] }}</li>
                    @endisset
                @endforeach
            </ol>
        </nav>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary">
                @if($icon)<i class="bi {{ $icon }} text-gold"></i> @endif
                {{ $title }}
            </h4>
            @if($subtitle)<p class="text-muted mb-0 fs-md">{{ $subtitle }}</p>@endif
        </div>
        <div>
            {{ $action ?? '' }}
        </div>
    </div>
</div>
