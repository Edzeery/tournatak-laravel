@php $currentLocale = app()->getLocale(); @endphp
<li class="nav-item">
    <div class="dropdown">
        <button class="nav-link dropdown-toggle d-flex align-items-center gap-1" data-bs-toggle="dropdown" style="background:none;border:none;">
            <i class="bi bi-globe2"></i>
            <span class="d-none d-lg-inline" style="font-size:0.85rem;">{{ $currentLocale === 'ar' ? 'عربي' : 'EN' }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="border-radius:12px;min-width:140px;">
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'ar' ? 'active' : '' }}" href="{{ route('lang.switch', 'ar') }}">
                    <span>عربي</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'en' ? 'active' : '' }}" href="{{ route('lang.switch', 'en') }}">
                    <span>English</span>
                </a>
            </li>
        </ul>
    </div>
</li>
