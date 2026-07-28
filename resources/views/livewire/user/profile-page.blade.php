@php $isRtl = isRtl(); @endphp
<div class="container py-4 container-page-md profile-wrap ">

    {{-- Profile Hero --}}
    <div class="profile-hero mb-4">
        <div class="profile-hero-bg"></div>
        <div class="profile-hero-content">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="profile-avatar">
                    <span class="profile-avatar-text">{{ mb_substr($user->name, 0, 1) }}</span>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h4 class="fw-bold mb-0 text-white">{{ $user->name }}</h4>
                        @if($user->is_verified)
                            <span class="profile-badge-verified"><i class="bi bi-patch-check-fill"></i> {{ __('app.verified') }}</span>
                        @else
                            <span class="profile-badge-unverified"><i class="bi bi-exclamation-circle"></i> {{ __('app.unverified') }}</span>
                        @endif
                    </div>
                    <p class="text-chrome-subtle mb-2">{{ $user->email }}</p>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <x-status-badge domain="role" status="{{ $user->role }}" set="bi" />
                        <span class="text-chrome-subtle fs-sm"><i class="bi bi-calendar-event"></i> {{ __('app.joined') }} {{ formatDate($user->created_at) }}</span>
                    </div>
                </div>
                <div class="d-none d-md-flex align-items-center gap-3">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-gold-outline rounded-md">
                        <i class="bi bi-arrow-{{ $isRtl ? 'right' : 'left' }}"></i> {{ __('app.back_to_dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="profile-alert profile-alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Column: Form --}}
        <div class="col-lg-8">
            {{-- Personal Information --}}
            <div class="profile-card mb-4">
                <div class="profile-card-header">
                    <div class="profile-card-icon"><i class="bi bi-person-lines-fill"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ __('app.personal_info') }}</h6>
                        <small class="text-chrome-muted">{{ __('app.personal_info_desc') }}</small>
                    </div>
                </div>
                <div class="profile-card-body">
                    <form wire:submit="save">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="profile-label">{{ __('app.full_name') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-person profile-input-icon"></i>
                                    <input type="text" class="profile-input" wire:model="full_name" required
                                        placeholder="{{ __('app.full_name_placeholder') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="profile-label">{{ __('app.username') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-at profile-input-icon"></i>
                                    <input type="text" class="profile-input" value="{{ $user->username }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="profile-label">{{ __('app.email') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-envelope profile-input-icon"></i>
                                    <input type="email" class="profile-input" value="{{ $user->email }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="profile-label">{{ __('app.date_of_birth') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-calendar3 profile-input-icon"></i>
                                    <input type="text" class="profile-input flatpickr-input"
                                        wire:model="profile_date_birth" placeholder="{{ __('app.select_date') }}"
                                        data-date-format="Y-m-d" data-alt-format="d/m/Y">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn profile-btn-save" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="bi bi-check-lg"></i> {{ __('app.save_changes') }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm"></span>
                                    {{ __('app.saving') }}...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Account Overview --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-icon"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ __('app.account_overview') }}</h6>
                        <small class="text-chrome-muted">{{ __('app.account_overview_desc') }}</small>
                    </div>
                </div>
                <div class="profile-card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-4">
                            <div class="profile-info-box">
                                <div class="profile-info-box-icon bg-gold-subtle text-gold">
                                    <i class="bi bi-shield-fill"></i>
                                </div>
                                <div class="profile-info-box-label">{{ __('app.role') }}</div>
                                <div class="profile-info-box-value">{{ ucfirst($user->role) }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="profile-info-box">
                                <div class="profile-info-box-icon bg-success-subtle text-success">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>
                                <div class="profile-info-box-label">{{ __('app.verification') }}</div>
                                <div class="profile-info-box-value">
                                    @if($user->is_verified)
                                        <span class="text-success">{{ __('app.verified') }}</span>
                                    @else
                                        <span class="text-danger">{{ __('app.unverified') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="profile-info-box">
                                <div class="profile-info-box-icon bg-info-subtle text-info">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="profile-info-box-label">{{ __('app.member_since') }}</div>
                                <div class="profile-info-box-value">{{ formatDate($user->created_at, 'd/m/Y') }}</div>
                            </div>
                        </div>
                        @if($user->teams->count())
                        <div class="col-sm-6 col-lg-4">
                            <div class="profile-info-box">
                                <div class="profile-info-box-icon bg-primary-subtle text-primary">
                                    <i class="bi bi-shield-fill"></i>
                                </div>
                                <div class="profile-info-box-label">{{ __('app.teams') }}</div>
                                <div class="profile-info-box-value">{{ $user->teams->count() }}</div>
                            </div>
                        </div>
                        @endif
                        @if($user->competitions->count())
                        <div class="col-sm-6 col-lg-4">
                            <div class="profile-info-box">
                                <div class="profile-info-box-icon bg-warning-subtle text-warning">
                                    <i class="bi bi-trophy-fill"></i>
                                </div>
                                <div class="profile-info-box-label">{{ __('app.competitions') }}</div>
                                <div class="profile-info-box-value">{{ $user->competitions->count() }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Stats --}}
            <div class="profile-card mb-4">
                <div class="profile-card-header">
                    <div class="profile-card-icon"><i class="bi bi-bar-chart-line"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ __('app.quick_stats') }}</h6>
                    </div>
                </div>
                <div class="profile-card-body">
                    <div class="profile-stat-row">
                        <div class="profile-stat-item">
                            <div class="profile-stat-number">{{ $user->teams->count() }}</div>
                            <div class="profile-stat-label">{{ __('app.teams') }}</div>
                        </div>
                        <div class="profile-stat-divider"></div>
                        <div class="profile-stat-item">
                            <div class="profile-stat-number">{{ $user->competitions->count() }}</div>
                            <div class="profile-stat-label">{{ __('app.competitions') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="profile-card mb-4">
                <div class="profile-card-header">
                    <div class="profile-card-icon"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ __('app.recent_activity') }}</h6>
                    </div>
                </div>
                <div class="profile-card-body">
                    @if($user->activities->count())
                        <div class="profile-activity-list">
                            @foreach($user->activities->take(5) as $activity)
                                <div class="profile-activity-item">
                                    <div class="profile-activity-dot"></div>
                                    <div class="profile-activity-content">
                                        <div class="fs-sm fw-semibold text-theme-primary">{{ $activity->description ?? __('app.activity') }}</div>
                                        <small class="text-theme-muted fs-xs">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-inbox d-block mb-2 fs-2xl text-theme-muted"></i>
                            <p class="text-theme-muted fs-sm mb-0">{{ __('app.no_activity') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="profile-card">
                <div class="profile-card-body">
                    <a href="{{ route('user.dashboard') }}" class="profile-action-btn profile-action-btn-primary">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>{{ __('app.go_to_dashboard') }}</span>
                    </a>
                    <a href="{{ route('teams.index') }}" class="profile-action-btn">
                        <i class="bi bi-shield-fill"></i>
                        <span>{{ __('app.browse_teams') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
