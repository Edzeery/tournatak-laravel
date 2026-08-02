<div class="container py-4 container-page-md">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-gear-fill me-2 fs-4 text-gold"></i>
        <h1 class="h3 mb-0">{{ $title }}</h1>
    </div>

    <form wire:submit="save">

        {{-- Appearance --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-palette-fill me-2 text-gold"></i>{{ __('app.appearance') }}
                </h5>
            </div>
            <div class="card-body">

                {{-- Theme --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('app.theme') }}</label>
                    <div class="d-flex gap-3">
                        <div class="form-check card card-body border p-3 text-center flex-fill cursor-pointer {{ $theme === 'light' ? 'border-gold bg-gold-subtle' : '' }}">
                            <input class="form-check-input d-none" type="radio" name="theme" id="themeLight" value="light" wire:model="theme">
                            <label class="form-check-label mb-1 cursor-pointer" for="themeLight">
                                <i class="bi bi-sun-fill fs-4 d-block mb-1"></i>
                                <span>{{ __('app.light') }}</span>
                            </label>
                        </div>
                        <div class="form-check card card-body border p-3 text-center flex-fill cursor-pointer {{ $theme === 'dark' ? 'border-gold bg-gold-subtle' : '' }}">
                            <input class="form-check-input d-none" type="radio" name="theme" id="themeDark" value="dark" wire:model="theme">
                            <label class="form-check-label mb-1 cursor-pointer" for="themeDark">
                                <i class="bi bi-moon-fill fs-4 d-block mb-1"></i>
                                <span>{{ __('app.dark') }}</span>
                            </label>
                        </div>
                        <div class="form-check card card-body border p-3 text-center flex-fill cursor-pointer {{ $theme === 'system' ? 'border-gold bg-gold-subtle' : '' }}">
                            <input class="form-check-input d-none" type="radio" name="theme" id="themeSystem" value="system" wire:model="theme">
                            <label class="form-check-label mb-1 cursor-pointer" for="themeSystem">
                                <i class="bi bi-laptop fs-4 d-block mb-1"></i>
                                <span>{{ __('app.system') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="border-secondary">

                {{-- Density --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('app.density') }}</label>
                    <div class="d-flex gap-3">
                        <div class="form-check card card-body border p-3 text-center flex-fill cursor-pointer {{ $density === 'comfortable' ? 'border-gold bg-gold-subtle' : '' }}">
                            <input class="form-check-input d-none" type="radio" name="density" id="densityComfortable" value="comfortable" wire:model="density">
                            <label class="form-check-label mb-1 cursor-pointer" for="densityComfortable">
                                <i class="bi bi-arrows-expand fs-4 d-block mb-1"></i>
                                <span>{{ __('app.comfortable') }}</span>
                            </label>
                        </div>
                        <div class="form-check card card-body border p-3 text-center flex-fill cursor-pointer {{ $density === 'compact' ? 'border-gold bg-gold-subtle' : '' }}">
                            <input class="form-check-input d-none" type="radio" name="density" id="densityCompact" value="compact" wire:model="density">
                            <label class="form-check-label mb-1 cursor-pointer" for="densityCompact">
                                <i class="bi bi-arrows-collapse fs-4 d-block mb-1"></i>
                                <span>{{ __('app.compact') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="border-secondary">

                {{-- Sidebar Collapsed --}}
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <label class="form-label fw-semibold mb-0">{{ __('app.sidebar_collapsed') }}</label>
                        <div class="form-text">{{ __('app.sidebar_collapsed_hint') }}</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="sidebarCollapsed" wire:model="sidebar_collapsed">
                        <label class="form-check-label" for="sidebarCollapsed"></label>
                    </div>
                </div>

            </div>
        </div>

        {{-- Language & Region --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-globe2 me-2 text-gold"></i>{{ __('app.language_region') }}
                </h5>
            </div>
            <div class="card-body">

                {{-- Locale --}}
                <div class="mb-3">
                    <label for="locale" class="form-label fw-semibold">{{ __('app.locale') }}</label>
                    <select class="form-select" id="locale" wire:model="locale">
                        <option value="ar">🇸🇦 {{ __('app.arabic') }}</option>
                        <option value="en">🇬🇧 {{ __('app.english') }}</option>
                        <option value="fr">🇫🇷 {{ __('app.french') }}</option>
                        <option value="es">🇪🇸 {{ __('app.spanish') }}</option>
                    </select>
                </div>

                {{-- Timezone --}}
                <div class="mb-3">
                    <label for="timezone" class="form-label fw-semibold">{{ __('app.timezone') }}</label>
                    <select class="form-select" id="timezone" wire:model="timezone">
                        <option value="Africa/Casablanca">{{ __('app.tz_casablanca') }}</option>
                        <option value="Africa/Cairo">{{ __('app.tz_cairo') }}</option>
                        <option value="Asia/Dubai">{{ __('app.tz_dubai') }}</option>
                        <option value="Asia/Riyadh">{{ __('app.tz_riyadh') }}</option>
                        <option value="Europe/London">{{ __('app.tz_london') }}</option>
                        <option value="Europe/Paris">{{ __('app.tz_paris') }}</option>
                        <option value="Europe/Madrid">{{ __('app.tz_madrid') }}</option>
                        <option value="America/New_York">{{ __('app.tz_new_york') }}</option>
                        <option value="America/Chicago">{{ __('app.tz_chicago') }}</option>
                        <option value="America/Los_Angeles">{{ __('app.tz_los_angeles') }}</option>
                        <option value="Asia/Tokyo">{{ __('app.tz_tokyo') }}</option>
                        <option value="Asia/Shanghai">{{ __('app.tz_shanghai') }}</option>
                    </select>
                </div>

                {{-- Date Format --}}
                <div class="mb-0">
                    <label for="dateFormat" class="form-label fw-semibold">{{ __('app.date_format') }}</label>
                    <select class="form-select" id="dateFormat" wire:model="date_format">
                        <option value="d/m/Y">{{ __('app.date_dmy') }} — 27/07/2026</option>
                        <option value="m/d/Y">{{ __('app.date_mdy') }} — 07/27/2026</option>
                        <option value="Y-m-d">{{ __('app.date_ymd') }} — 2026-07-27</option>
                        <option value="d M Y">{{ __('app.date_dmy_short') }} — 27 Jul 2026</option>
                        <option value="M d, Y">{{ __('app.date_mdy_long') }} — Jul 27, 2026</option>
                    </select>
                </div>

            </div>
        </div>

        {{-- Notification Preferences --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bell-fill me-2 text-gold"></i>{{ __('app.notifications') }}
                </h5>
            </div>
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <label class="form-label fw-semibold mb-0">{{ __('app.email_notifications') }}</label>
                        <div class="form-text">{{ __('app.email_notifications_hint') }}</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="notifyEmail" wire:model="notify_email">
                        <label class="form-check-label" for="notifyEmail"></label>
                    </div>
                </div>

                <hr class="border-secondary">

                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <label class="form-label fw-semibold mb-0">{{ __('app.push_notifications') }}</label>
                        <div class="form-text">{{ __('app.push_notifications_hint') }}</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="notifyPush" wire:model="notify_push">
                        <label class="form-check-label" for="notifyPush"></label>
                    </div>
                </div>

            </div>
        </div>

        {{-- Save Button --}}
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-gold px-4" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">
                    <i class="bi bi-check-lg me-1"></i>{{ __('app.save') }}
                </span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>{{ __('app.saving') }}
                </span>
            </button>
        </div>

    </form>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('toast', (payload) => {
                if (payload.type !== 'success') {
                    return;
                }
                const theme = document.querySelector('input[name="theme"]:checked')?.value;
                if (theme) {
                    if (theme === 'system') {
                        localStorage.removeItem('theme');
                        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        document.documentElement.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
                    } else {
                        localStorage.setItem('theme', theme);
                        document.documentElement.setAttribute('data-bs-theme', theme);
                    }
                    if (window.updateThemeIcon) {
                        window.updateThemeIcon(document.documentElement.getAttribute('data-bs-theme'));
                    }
                }
            });
        });
    </script>
@endpush
