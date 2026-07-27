@props([
    'rows' => 5,
    'type' => 'table',
    'table' => false,
])

@if($type === 'table')
    {{-- Table skeleton --}}
    <div class="skeleton-wrapper">
        @foreach(range(1, $rows) as $i)
            <div class="skeleton-row d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:rgba(255,255,255,0.06) !important;">
                <div class="skeleton-avatar skeleton-pulse"></div>
                <div class="flex-grow-1">
                    <div class="skeleton-line skeleton-pulse mb-2" style="width:{{ rand(40,75) }}%;"></div>
                    <div class="skeleton-line skeleton-pulse opacity-50" style="width:{{ rand(20,50) }}%;"></div>
                </div>
                <div class="d-flex gap-2">
                    <div class="skeleton-btn skeleton-pulse"></div>
                    <div class="skeleton-btn skeleton-pulse"></div>
                </div>
            </div>
        @endforeach
    </div>
@elseif($type === 'card')
    {{-- Card skeleton --}}
    <div class="row g-4">
        @foreach(range(1, $rows) as $i)
            <div class="col-md-6 col-lg-4">
                <div class="card card-dark rounded-xl">
                    <div class="card-body p-4">
                        <div class="skeleton-line skeleton-pulse mb-3" style="width:60%;height:20px;"></div>
                        <div class="skeleton-line skeleton-pulse mb-2" style="width:100%;"></div>
                        <div class="skeleton-line skeleton-pulse mb-2" style="width:80%;"></div>
                        <div class="skeleton-line skeleton-pulse" style="width:40%;"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    {{-- Lines skeleton --}}
    <div class="skeleton-wrapper">
        @foreach(range(1, $rows) as $i)
            <div class="skeleton-line skeleton-pulse mb-3" style="width:{{ rand(50,100) }}%;"></div>
        @endforeach
    </div>
@endif
