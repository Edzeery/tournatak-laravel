@php
    $width = $width ?? 400;
    $height = $height ?? 600;
    $sportType = $sportType ?? 'football';
    $positions = $positions ?? [];
    $jerseyClass = $jerseyClass ?? 'team1-jersey';
    $isFutsal = $sportType === 'futsal';
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 600" width="{{ $width }}" height="{{ $height }}" dir="ltr" style="display:block;width:100%;height:100%;position:absolute;inset:0;">
    <defs>
        <linearGradient id="pg-{{ md5($sportType . $jerseyClass) }}" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" style="stop-color:#1a5c2e;stop-opacity:1" />
            <stop offset="50%" style="stop-color:#1e6b34;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#1a5c2e;stop-opacity:1" />
        </linearGradient>
        <filter id="playerShadow-{{ md5($sportType) }}">
            <feDropShadow dx="0" dy="1" stdDeviation="2" flood-color="rgba(0,0,0,0.5)"/>
        </filter>
        <filter id="glow-{{ md5($sportType) }}">
            <feGaussianBlur stdDeviation="2" result="blur"/>
            <feMerge>
                <feMergeNode in="blur"/>
                <feMergeNode in="SourceGraphic"/>
            </feMerge>
        </filter>
    </defs>

    {{-- Pitch Background --}}
    <rect x="0" y="0" width="400" height="600" fill="url(#pg-{{ md5($sportType . $jerseyClass) }})"/>

    {{-- Grass pattern --}}
    @for($i = 0; $i < 15; $i++)
        <rect x="0" y="{{ $i * 40 }}" width="400" height="20" fill="{{ $i % 2 === 0 ? 'rgba(255,255,255,0.02)' : 'transparent' }}"/>
    @endfor

    @php
        $lineColor = 'rgba(255,255,255,0.6)';
        $lineWidth = 1;
        $bm = 18;
        $bw = 400 - (2 * $bm);
        $bh = 600 - (2 * $bm);

        if ($isFutsal) {
            $penW = $bw * 0.60; $penH = $bh * 0.14;
            $gaW = $bw * 0.30; $gaH = $bh * 0.06;
        } else {
            $penW = $bw * 0.68; $penH = $bh * 0.16;
            $gaW = $bw * 0.36; $gaH = $bh * 0.06;
        }
        $cR = min($bw, $bh) * 0.12;
        $cA = 8;
        $psD = $penH * 0.68;
    @endphp

    {{-- Boundary --}}
    <rect x="{{ $bm }}" y="{{ $bm }}" width="{{ $bw }}" height="{{ $bh }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>

    {{-- Center Line --}}
    <line x1="{{ $bm }}" y1="300" x2="{{ 400 - $bm }}" y2="300" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>

    {{-- Center Circle --}}
    <circle cx="200" cy="300" r="{{ $cR }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>
    <circle cx="200" cy="300" r="2" fill="{{ $lineColor }}"/>

    {{-- Top Penalty Area --}}
    @php $tpx = 200 - ($penW / 2); $tpy = $bm; @endphp
    <rect x="{{ $tpx }}" y="{{ $tpy }}" width="{{ $penW }}" height="{{ $penH }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>
    <rect x="{{ 200 - ($gaW / 2) }}" y="{{ $tpy + ($penH - $gaH) }}" width="{{ $gaW }}" height="{{ $gaH }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>
    <circle cx="200" cy="{{ $tpy + $psD }}" r="2" fill="{{ $lineColor }}"/>

    {{-- Top Penalty Arc --}}
    @php $tacY = $tpy + $psD; $tacR = $cR * 1.2; @endphp
    <path d="M {{ 200 + ($penW / 2) }} {{ $tacY - sqrt(max(0, $tacR*$tacR - ($penW/2)*($penW/2))) }} A {{ $tacR }} {{ $tacR }} 0 0 1 {{ 200 - ($penW / 2) }} {{ $tacY - sqrt(max(0, $tacR*$tacR - ($penW/2)*($penW/2))) }}"
          fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"
          clip-path="url(#tacClip)"/>
    <defs><clipPath id="tacClip"><rect x="{{ $bm }}" y="{{ $tpy + $penH }}" width="{{ $bw }}" height="{{ $cR * 2 }}"/></clipPath></defs>

    {{-- Bottom Penalty Area --}}
    @php $bpy = $bm + $bh - $penH; @endphp
    <rect x="{{ 200 - ($penW / 2) }}" y="{{ $bpy }}" width="{{ $penW }}" height="{{ $penH }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>
    <rect x="{{ 200 - ($gaW / 2) }}" y="{{ $bpy }}" width="{{ $gaW }}" height="{{ $gaH }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>
    <circle cx="200" cy="{{ $bpy + $penH - $psD }}" r="2" fill="{{ $lineColor }}"/>

    {{-- Bottom Penalty Arc --}}
    @php $bacY = $bpy + $penH - $psD; @endphp
    <path d="M {{ 200 + ($penW / 2) }} {{ $bacY + sqrt(max(0, $tacR*$tacR - ($penW/2)*($penW/2))) }} A {{ $tacR }} {{ $tacR }} 0 0 0 {{ 200 - ($penW / 2) }} {{ $bacY + sqrt(max(0, $tacR*$tacR - ($penW/2)*($penW/2))) }}"
          fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"
          clip-path="url(#bacClip)"/>
    <defs><clipPath id="bacClip"><rect x="{{ $bm }}" y="{{ $bacY - $cR * 2 }}" width="{{ $bw }}" height="{{ $cR * 2 }}"/></clipPath></defs>

    {{-- Corner Arcs --}}
    <path d="M {{ $bm }} {{ $bm + $cA }} A {{ $cA }} {{ $cA }} 0 0 1 {{ $bm + $cA }} {{ $bm }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>
    <path d="M {{ 400 - $bm - $cA }} {{ $bm }} A {{ $cA }} {{ $cA }} 0 0 1 {{ 400 - $bm }} {{ $bm + $cA }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>
    <path d="M {{ $bm }} {{ 600 - $bm - $cA }} A {{ $cA }} {{ $cA }} 0 0 0 {{ $bm + $cA }} {{ 600 - $bm }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>
    <path d="M {{ 400 - $bm - $cA }} {{ 600 - $bm }} A {{ $cA }} {{ $cA }} 0 0 0 {{ 400 - $bm }} {{ 600 - $bm - $cA }}" fill="none" stroke="{{ $lineColor }}" stroke-width="{{ $lineWidth }}"/>

    {{-- Player Positions --}}
    @foreach($positions as $idx => $pos)
        @php
            $px = ($pos['x'] ?? 50) / 100 * 400;
            $py = ($pos['y'] ?? 50) / 100 * 600;
            $label = $pos['position'] ?? $pos['label'] ?? $pos['role'] ?? '?';
            $playerName = $pos['player_name'] ?? '';
            $jerseyNum = $pos['jersey_number'] ?? '';
            $isCaptain = $pos['is_captain'] ?? false;
            $r = 20;
        @endphp

        {{-- Outer glow for captain --}}
        @if($isCaptain)
            <circle cx="{{ $px }}" cy="{{ $py }}" r="{{ $r + 4 }}" fill="none" stroke="#ffc107" stroke-width="2" opacity="0.6" filter="url(#glow-{{ md5($sportType) }})"/>
        @endif

        {{-- Shadow --}}
        <circle cx="{{ $px }}" cy="{{ $py + 2 }}" r="{{ $r }}" fill="rgba(0,0,0,0.3)"/>

        {{-- Jersey circle --}}
        <circle cx="{{ $px }}" cy="{{ $py }}" r="{{ $r }}"
                fill="{{ $jerseyClass === 'team2-jersey' ? '#c62828' : '#1a237e' }}"
                stroke="{{ $isCaptain ? '#ffc107' : 'rgba(255,255,255,0.9)' }}"
                stroke-width="{{ $isCaptain ? 3 : 2 }}"/>

        {{-- Jersey number --}}
        <text x="{{ $px }}" y="{{ $py }}" text-anchor="middle" dominant-baseline="central"
              font-family="Cairo, sans-serif" font-size="13" font-weight="800" fill="#ffffff">
            {{ $jerseyNum ?: $label }}
        </text>

        {{-- Player name below --}}
        @if($playerName)
            <text x="{{ $px }}" y="{{ $py + $r + 12 }}" text-anchor="middle"
                  font-family="Cairo, sans-serif" font-size="9" font-weight="700" fill="#ffffff">
                <tspan paint-order="stroke" stroke="rgba(0,0,0,0.8)" stroke-width="3" stroke-linejoin="round">{{ $playerName }}</tspan>
            </text>
            {{-- Position abbreviation --}}
            <text x="{{ $px }}" y="{{ $py + $r + 22 }}" text-anchor="middle"
                  font-family="Cairo, sans-serif" font-size="7" font-weight="500" fill="rgba(255,255,255,0.6)">
                {{ $label }}
            </text>
        @endif

        {{-- Captain badge --}}
        @if($isCaptain)
            <circle cx="{{ $px + $r - 2 }}" cy="{{ $py - $r + 2 }}" r="7" fill="#ffc107" stroke="#1a1a2e" stroke-width="1.5"/>
            <text x="{{ $px + $r - 2 }}" y="{{ $py - $r + 2 }}" text-anchor="middle" dominant-baseline="central"
                  font-family="Cairo, sans-serif" font-size="7" font-weight="900" fill="#1a1a2e">C</text>
        @endif
    @endforeach
</svg>
