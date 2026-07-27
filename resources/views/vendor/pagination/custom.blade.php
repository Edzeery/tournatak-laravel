@if ($paginator->hasPages())
    <nav class="d-flex align-items-center justify-content-between flex-wrap gap-3 pagination-wrap">

        {{-- Mobile: Simple prev/next --}}
        <div class="d-flex d-sm-none align-items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="page-btn page-btn-disabled">
                    <i class="bi bi-chevron-{{ isRtl() ? 'right' : 'left' }}"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-btn page-btn-link" rel="prev">
                    <i class="bi bi-chevron-{{ isRtl() ? 'right' : 'left' }}"></i>
                </a>
            @endif

            <span class="page-info-mobile">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-btn page-btn-link" rel="next">
                    <i class="bi bi-chevron-{{ isRtl() ? 'left' : 'right' }}"></i>
                </a>
            @else
                <span class="page-btn page-btn-disabled">
                    <i class="bi bi-chevron-{{ isRtl() ? 'left' : 'right' }}"></i>
                </span>
            @endif
        </div>

        {{-- Desktop: Full pagination --}}
        <div class="d-none d-sm-flex align-items-center gap-3">

            {{-- Info --}}
            <div class="page-info">
                <span class="page-info-num">{{ $paginator->firstItem() }}</span>
                <span class="page-info-sep">-</span>
                <span class="page-info-num">{{ $paginator->lastItem() }}</span>
                <span class="page-info-of">{{ __('app.of') }}</span>
                <span class="page-info-num">{{ $paginator->total() }}</span>
            </div>

            {{-- Page buttons --}}
            <ul class="pagination mb-0">
                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-btn page-btn-disabled">
                            <i class="bi bi-chevron-{{ isRtl() ? 'right' : 'left' }}"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-btn page-btn-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <i class="bi bi-chevron-{{ isRtl() ? 'right' : 'left' }}"></i>
                        </a>
                    </li>
                @endif

                {{-- Pages --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-btn page-btn-ellipsis">{{ $element }}</span>
                        </li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-btn page-btn-active">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-btn page-btn-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-btn page-btn-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            <i class="bi bi-chevron-{{ isRtl() ? 'left' : 'right' }}"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-btn page-btn-disabled">
                            <i class="bi bi-chevron-{{ isRtl() ? 'left' : 'right' }}"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
