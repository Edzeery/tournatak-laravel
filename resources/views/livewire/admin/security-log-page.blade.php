<div>
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-gold bg-opacity-10 text-gold rounded-3 d-flex align-items-center justify-content-center"
                style="width:44px;height:44px;font-size:1.2rem;">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="font-size:1.1rem;color:var(--dark);">{{ __('app.security_log') }}</h5>
                <small style="color:#94a3b8;">Security Log</small>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:#fff;">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:#64748b;">
                        <i class="bi bi-search me-1"></i>{{ __('app.search') }}
                    </label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('app.search') }}..."
                        aria-label="{{ __('app.search') }}"
                        style="border-radius:8px;border:1px solid #e2e8f0;font-size:0.85rem;padding:0.5rem 0.75rem;">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:#64748b;">
                        <i class="bi bi-person me-1"></i>{{ __('app.users') }}
                    </label>
                    <select class="form-select" wire:model.live="filterUser"
                        style="border-radius:8px;border:1px solid #e2e8f0;font-size:0.85rem;padding:0.5rem 0.75rem;">
                        <option value="">{{ __('app.all') }} {{ __('app.users') }}</option>
                        @foreach($users as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:#64748b;">
                        <i class="bi bi-tag me-1"></i>{{ __('app.event') }}
                    </label>
                    <select class="form-select" wire:model.live="filterEvent"
                        style="border-radius:8px;border:1px solid #e2e8f0;font-size:0.85rem;padding:0.5rem 0.75rem;">
                        <option value="">{{ __('app.all') }} {{ __('app.event') }}</option>
                        @foreach($eventTypes as $event)
                            <option value="{{ $event }}">{{ $event }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-sm w-100" wire:click="$set('search', ''); $set('filterUser', null); $set('filterEvent', null);"
                        style="border-radius:8px;border:1px solid #e2e8f0;color:#64748b;background:#f8fafc;font-size:0.8rem;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;background:#fff;">
        @if($records->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                            <th class="fw-semibold" style="font-size:0.78rem;color:#64748b;padding:0.85rem 1rem;">{{ __('app.user') }}</th>
                            <th class="fw-semibold" style="font-size:0.78rem;color:#64748b;padding:0.85rem 1rem;">{{ __('app.event') }}</th>
                            <th class="fw-semibold" style="font-size:0.78rem;color:#64748b;padding:0.85rem 1rem;">{{ __('app.ip_address') }}</th>
                            <th class="fw-semibold" style="font-size:0.78rem;color:#64748b;padding:0.85rem 1rem;">{{ __('app.device') }}</th>
                            <th class="fw-semibold" style="font-size:0.78rem;color:#64748b;padding:0.85rem 1rem;">{{ __('app.time') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:0.75rem 1rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                            style="width:32px;height:32px;font-size:0.75rem;background:linear-gradient(135deg,#f59e0b,#d97706);">
                                            {{ mb_substr($record->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size:0.85rem;color:var(--dark);">{{ $record->user->name ?? __('app.deleted') }}</div>
                                            <small style="color:#94a3b8;font-size:0.72rem;">{{ $record->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    @php
                                        $badgeClass = match(true) {
                                            str_contains($record->event ?? '', 'login') && !str_contains($record->event ?? '', 'failed') && !str_contains($record->event ?? '', 'logout') => 'bg-success bg-opacity-10 text-success',
                                            str_contains($record->event ?? '', 'logout') => 'bg-primary bg-opacity-10 text-primary',
                                            str_contains($record->event ?? '', 'failed') => 'bg-danger bg-opacity-10 text-danger',
                                            str_contains($record->event ?? '', '2fa') || str_contains($record->event ?? '', 'two_factor') => 'bg-warning bg-opacity-10 text-warning',
                                            default => 'bg-secondary bg-opacity-10 text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} fw-semibold" style="font-size:0.75rem;padding:0.35rem 0.65rem;border-radius:6px;">
                                        {{ $record->event }}
                                    </span>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <span style="font-size:0.83rem;color:#475569;font-family:monospace;">{{ $record->ip_address ?? '—' }}</span>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <span style="font-size:0.8rem;color:#64748b;max-width:200px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $record->user_agent ?? '' }}">
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
                                <td style="padding:0.75rem 1rem;">
                                    <span style="font-size:0.8rem;color:#64748b;" title="{{ $record->created_at }}">
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
                    <i class="bi bi-shield-check" style="font-size:2.5rem;color:#cbd5e1;"></i>
                </div>
                <h6 class="fw-bold" style="color:#64748b;">{{ __('app.no_records') }}</h6>
                <small style="color:#94a3b8;">No security events found matching your filters.</small>
            </div>
        @endif
    </div>
</div>
