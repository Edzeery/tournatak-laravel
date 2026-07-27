@php $currentLocale = app()->getLocale(); @endphp
<li class="nav-item">
    <div class="dropdown">
        <button class="nav-link dropdown-toggle d-flex align-items-center gap-1" data-bs-toggle="dropdown" style="background:none;border:none;color:rgba(255,255,255,0.7) !important;">
            <i class="bi bi-globe2"></i>
            <span class="d-none d-lg-inline" style="font-size:0.85rem;">{{ match($currentLocale) { 'ar' => 'عربي', 'en' => 'EN', 'fr' => 'FR', 'es' => 'ES', default => 'EN' } }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="border-radius:12px;min-width:160px;background:#1a1f35;">
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'ar' ? 'active' : '' }}"
                   href="{{ route('lang.switch', 'ar') }}"
                   style="color:{{ $currentLocale === 'ar' ? '#ffc107' : 'rgba(255,255,255,0.7)' }};font-weight:600;">
                    <span style="font-size:1.2rem;">🇸🇦</span>
                    <span>العربية</span>
                    @if($currentLocale === 'ar')
                        <i class="bi bi-check-lg ms-auto" style="color:#ffc107;"></i>
                    @endif
                </a>
            </li>
            <li><hr class="dropdown-divider my-1" style="border-color:rgba(255,255,255,0.06);"></li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'en' ? 'active' : '' }}"
                   href="{{ route('lang.switch', 'en') }}"
                   style="color:{{ $currentLocale === 'en' ? '#ffc107' : 'rgba(255,255,255,0.7)' }};font-weight:600;">
                    <span style="font-size:1.2rem;">🇬🇧</span>
                    <span>English</span>
                    @if($currentLocale === 'en')
                        <i class="bi bi-check-lg ms-auto" style="color:#ffc107;"></i>
                    @endif
                </a>
            </li>
            <li><hr class="dropdown-divider my-1" style="border-color:rgba(255,255,255,0.06);"></li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'fr' ? 'active' : '' }}"
                   href="{{ route('lang.switch', 'fr') }}"
                   style="color:{{ $currentLocale === 'fr' ? '#ffc107' : 'rgba(255,255,255,0.7)' }};font-weight:600;">
                    <span style="font-size:1.2rem;">🇫🇷</span>
                    <span>Français</span>
                    @if($currentLocale === 'fr')
                        <i class="bi bi-check-lg ms-auto" style="color:#ffc107;"></i>
                    @endif
                </a>
            </li>
            <li><hr class="dropdown-divider my-1" style="border-color:rgba(255,255,255,0.06);"></li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'es' ? 'active' : '' }}"
                   href="{{ route('lang.switch', 'es') }}"
                   style="color:{{ $currentLocale === 'es' ? '#ffc107' : 'rgba(255,255,255,0.7)' }};font-weight:600;">
                    <span style="font-size:1.2rem;">🇪🇸</span>
                    <span>Español</span>
                    @if($currentLocale === 'es')
                        <i class="bi bi-check-lg ms-auto" style="color:#ffc107;"></i>
                    @endif
                </a>
            </li>
        </ul>
    </div>
</li>
