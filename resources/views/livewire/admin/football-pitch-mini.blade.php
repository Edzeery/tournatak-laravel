@php
    $positions = $positions ?? [];
    $sportType = $sportType ?? 'football';
    $isFutsal = $sportType === 'futsal';
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 200" width="150" height="200" dir="ltr" style="display:block;">
    {{-- Pitch Background --}}
    <rect x="0" y="0" width="150" height="200" fill="#2d8a4e" rx="3"/>

    @php
        $lc = '#ffffff';
        $lw = 0.7;
        $mx = 5;
        $pw = 150 - (2 * $mx);  -- 140
        $ph = 200 - (2 * $mx);  -- 190
        $cx = 150 / 2;
        $cy = 200 / 2;

        if ($isFutsal) {
            $penH = $ph * 0.14;
            $gaH = $ph * 0.055;
        } else {
            $penH = $ph * 0.16;
            $gaH = $ph * 0.055;
        }
        $penW = $pw * 0.60;
        $gaW = $pw * 0.30;
        $centerR = min($pw, $ph) * 0.11;
        $cornerR = 4;
    @endphp

    {{-- Boundary --}}
    <rect x="{{ $mx }}" y="{{ $mx }}" width="{{ $pw }}" height="{{ $ph }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>

    {{-- Center Line --}}
    <line x1="{{ $mx }}" y1="{{ $cy }}" x2="{{ 150 - $mx }}" y2="{{ $cy }}" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>

    {{-- Center Circle --}}
    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $centerR }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>
    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="1" fill="{{ $lc }}"/>

    {{-- Top Penalty Area --}}
    @php
        $topPenX = $cx - ($penW / 2);
        $topGAy = $mx + ($penH - $gaH);
    @endphp
    <rect x="{{ $topPenX }}" y="{{ $mx }}" width="{{ $penW }}" height="{{ $penH }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>
    <rect x="{{ $cx - ($gaW / 2) }}" y="{{ $topGAy }}" width="{{ $gaW }}" height="{{ $gaH }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>

    {{-- Bottom Penalty Area --}}
    @php
        $botPenY = 200 - $mx - $penH;
        $botGAy = $botPenY;
    @endphp
    <rect x="{{ $topPenX }}" y="{{ $botPenY }}" width="{{ $penW }}" height="{{ $penH }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>
    <rect x="{{ $cx - ($gaW / 2) }}" y="{{ $botGAy }}" width="{{ $gaW }}" height="{{ $gaH }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>

    {{-- Corner Arcs --}}
    <path d="M {{ $mx }} {{ $mx + $cornerR }} A {{ $cornerR }} {{ $cornerR }} 0 0 1 {{ $mx + $cornerR }} {{ $mx }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>
    <path d="M {{ 150 - $mx - $cornerR }} {{ $mx }} A {{ $cornerR }} {{ $cornerR }} 0 0 1 {{ 150 - $mx }} {{ $mx + $cornerR }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>
    <path d="M {{ $mx }} {{ 200 - $mx - $cornerR }} A {{ $cornerR }} {{ $cornerR }} 0 0 0 {{ $mx + $cornerR }} {{ 200 - $mx }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>
    <path d="M {{ 150 - $mx - $cornerR }} {{ 200 - $mx }} A {{ $cornerR }} {{ $cornerR }} 0 0 0 {{ 150 - $mx }} {{ 200 - $mx - $cornerR }}" fill="none" stroke="{{ $lc }}" stroke-width="{{ $lw }}"/>

    {{-- Player Positions --}}
    @foreach($positions as $pos)
        @php
            $px = ($pos['x'] ?? 50) / 100 * 150;
            $py = ($pos['y'] ?? 50) / 100 * 200;
            $label = $pos['position'] ?? $pos['label'] ?? $pos['role'] ?? '?';
            $r = 9;
        @endphp
        <circle cx="{{ $px }}" cy="{{ $py }}" r="{{ $r }}" fill="#ffffff" stroke="#1e293b" stroke-width="1.2"/>
        <text x="{{ $px }}" y="{{ $py }}" text-anchor="middle" dominant-baseline="central"
              font-family="Cairo, sans-serif" font-size="5.5" font-weight="700" fill="#1e293b">
            {{ $label }}
        </text>
    @endforeach
</svg>
