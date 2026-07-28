<div>
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-gold bg-opacity-10 text-gold rounded-3 d-flex align-items-center justify-content-center w-44 h-44 fs-2xl">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold fs-lg text-dark-theme">{{ __('app.security_log') }}</h5>
                <small class="text-slate-400">Security Log</small>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4 card-white">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold th-cell-sm">
                        <i class="bi bi-search me-1"></i>{{ __('app.search') }}
                    </label>
                    <input type="text" class="form-control input-filter" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('app.search') }}..."
                        aria-label="{{ __('app.search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold th-cell-sm">
                        <i class="bi bi-person me-1"></i>{{ __('app.users') }}
                    </label>
                    <select class="form-select input-filter" wire:model.live="filterUser">
                        <option value="">{{ __('app.all') }} {{ __('app.users') }}</option>
                        @foreach($users as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold th-cell-sm">
                        <i class="bi bi-tag me-1"></i>{{ __('app.event') }}
                    </label>
                    <select class="form-select input-filter" wire:model.live="filterEvent">
                        <option value="">{{ __('app.all') }} {{ __('app.event') }}</option>
                        @foreach($eventTypes as $event)
                            <option value="{{ $event }}">{{ $event }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-sm w-100 btn-reset-light" wire:click="$set('search', ''); $set('filterUser', null); $set('filterEvent', null);">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm card-white">
        @if($records->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="th-head">
                            <th class="fw-semibold th-cell">{{ __('app.user') }}</th>
                            <th class="fw-semibold th-cell">{{ __('app.event') }}</th>
                            <th class="fw-semibold th-cell">{{ __('app.ip_address') }}</th>
                            <th class="fw-semibold th-cell">{{ __('app.device') }}</th>
                            <th class="fw-semibold th-cell">{{ __('app.time') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr class="border-row-light">
                                <td class="cell-pad">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white w-32 h-32 fs-sm avatar-gradient-amber">
                                            {{ mb_substr($record->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold fs-base text-dark-theme">{{ $record->user->name ?? __('app.deleted') }}</div>
                                            <small class="text-slate-400">{{ $record->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="cell-pad">
                                    @php
                                        $badgeClass = match(true) {
                                            str_contains($record->event ?? '', 'login') && !str_contains($record->event ?? '', 'failed') && !str_contains($record->event ?? '', 'logout') => 'bg-success bg-opacity-10 text-success',
                                            str_contains($record->event ?? '', 'logout') => 'bg-primary bg-opacity-10 text-primary',
                                            str_contains($record->event ?? '', 'failed') => 'bg-danger bg-opacity-10 text-danger',
                                            str_contains($record->event ?? '', '2fa') || str_contains($record->event ?? '', 'two_factor') => 'bg-warning bg-opacity-10 text-warning',
                                            default => 'bg-secondary bg-opacity-10 text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} fw-semibold fs-sm">
                                        {{ $record->event }}
                                    </span>
                                </td>
                                <td class="cell-pad">
                                    <span class="fs-base text-slate-600 font-monospace">{{ $record->ip_address ?? '—' }}</span>
                                </td>
                                <td class="cell-pad">
                                    <span class="fs-08 text-slate-500 text-truncate-sm" title="{{ $record->user_agent ?? '' }}">
                                        @if($record->user_agent)
                                            @php
                                                $ua = $record->user_agent;
                                                $device = 'Unknown';
                                                if (str_contains($ua, 'Windows')) $device = 'Windows';
                                                elseif (str_contains($ua, 'Mac')) $device = 'MacOS';
                                                elseif (str_contains($ua, 'Linux')) $device = 'Linux';
                                                elseif (str_contains($ua, 'Android')) $device = 'Android';
                                                elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $device = 'iOS';
                                            @endphp
                                            <i class="bi bi-laptop me-1"></i>{{ $device }}
                                        @else
                                            —
                                        @endif
                                    </span>
                                </td>
                                <td class="cell-pad">
                                    <span class="fs-08 text-slate-500" title="{{ $record->created_at }}">
                                        {{ $record->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 d-flex justify-content-center">
                {{ $records->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-shield-check fs-4xl text-slate"></i>
                </div>
                <h6 class="fw-bold text-slate-500">{{ __('app.no_records') }}</h6>
                <small class="text-slate-400">No security events found matching your filters.</small>
            </div>
        @endif
    </div>
</div>
