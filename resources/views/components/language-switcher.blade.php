@php $currentLocale = app()->getLocale(); @endphp
<li class="nav-item">
    <div class="dropdown">
        <button class="nav-link dropdown-toggle d-flex align-items-center gap-1 text-primary" data-bs-toggle="dropdown">
            <i class="bi bi-globe2"></i>
            <span
                class="d-none d-lg-inline fs-base">{{ match ($currentLocale) {'ar' => 'عربي','en' => 'EN','fr' => 'FR','es' => 'ES',default => 'EN'} }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-lg-custom dropdown-w-160 px-2">
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'ar' ? 'active lang-item-active  ' : 'lang-item-inactive' }}"
                    href="{{ route('lang.switch', 'ar') }}">
                    <span class="fs-xl ">🇸🇦</span>
                    <span>العربية</span>
                    @if ($currentLocale === 'ar')
                        <i class="bi bi-check-lg ms-auto text-gold"></i>
                    @endif
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'en' ? 'active lang-item-active' : 'lang-item-inactive' }}"
                    href="{{ route('lang.switch', 'en') }}">
                    <span class="fs-xl">🇬🇧</span>
                    <span>English</span>
                    @if ($currentLocale === 'en')
                        <i class="bi bi-check-lg ms-auto text-gold"></i>
                    @endif
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'fr' ? 'active lang-item-active' : 'lang-item-inactive' }}"
                    href="{{ route('lang.switch', 'fr') }}">
                    <span class="fs-xl">🇫🇷</span>
                    <span>Français</span>
                    @if ($currentLocale === 'fr')
                        <i class="bi bi-check-lg ms-auto text-gold"></i>
                    @endif
                </a>
            </li>
            {{-- <li>
                <hr class="dropdown-divider my-1 border-chrome-bottom">
            </li> --}}
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 {{ $currentLocale === 'es' ? 'active lang-item-active' : 'lang-item-inactive' }}"
                    href="{{ route('lang.switch', 'es') }}">
                    <span class="fs-xl">🇪🇸</span>
                    <span>Español</span>
                    @if ($currentLocale === 'es')
                        <i class="bi bi-check-lg ms-auto text-gold"></i>
                    @endif
                </a>
            </li>
        </ul>
    </div>
</li>
