@php $isRtl = isRtl(); @endphp
<div class="container py-4 container-page-md">
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-theme-primary">
                <i class="bi bi-person-plus-fill text-gold me-2"></i> {{ __('app.my_registrations') }}
            </h4>
            <p class="text-theme-muted mb-0 fs-md">{{ __('app.my_registrations_desc') }}</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Register Form --}}
    <div class="card border-0 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">
                <i class="bi bi-plus-circle text-gold me-2"></i> {{ __('app.register_for_competition') }}
            </h6>
            <form wire:submit="register">
                <div class="row g-3">
                    {{-- Participant Type --}}
                    <div class="col-md-4">
                        <label class="form-label fs-sm fw-medium">{{ __('app.participant_type') }}</label>
                        <select class="form-select" wire:model.live="participantType">
                            <option value="individual">{{ __('app.participant_type_individual') }}</option>
                            <option value="team">{{ __('app.team') }}</option>
                        </select>
                    </div>

                    {{-- Competition --}}
                    <div class="col-md-4">
                        <label class="form-label fs-sm fw-medium">{{ __('app.competition') }}</label>
                        <select class="form-select" wire:model="competition_id">
                            <option value="">-- {{ __('app.select_competition') }} --</option>
                            @if ($participantType === 'individual')
                                @foreach ($availableIndividualCompetitions as $comp)
                                    <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                                @endforeach
                            @else
                                @foreach ($availableTeamCompetitions as $comp)
                                    <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('competition_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Team (only for team type) --}}
                    @if ($participantType === 'team')
                        <div class="col-md-4">
                            <label class="form-label fs-sm fw-medium">{{ __('app.team') }}</label>
                            <select class="form-select" wire:model="team_id">
                                <option value="">-- {{ __('app.select_team') }} --</option>
                                @foreach ($userTeams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    @endif

                    {{-- Submit --}}
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary-sport">
                            <i class="bi bi-send me-1"></i> {{ __('app.submit_registration') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Current Registrations --}}
    <div class="card border-0">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">
                <i class="bi bi-list-check text-gold me-2"></i> {{ __('app.current_registrations') }}
            </h6>

            @if ($individualRegistrations->isEmpty() && $teamRegistrations->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox d-block mb-2 fs-4xl text-slate"></i>
                    <p class="text-theme-muted fs-md">{{ __('app.no_registrations_yet') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="fs-xs">{{ __('app.competition') }}</th>
                                <th class="fs-xs">{{ __('app.type') }}</th>
                                <th class="fs-xs">{{ __('app.participant') }}</th>
                                <th class="fs-xs">{{ __('app.status') }}</th>
                                <th class="fs-xs">{{ __('app.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($individualRegistrations as $reg)
                                <tr>
                                    <td class="fw-bold fs-base">{{ $reg->competition->name }}</td>
                                    <td><span class="badge bg-info-subtle text-info fs-sm">{{ __('app.participant_type_individual') }}</span></td>
                                    <td class="fs-base">{{ $reg->user?->name ?? '—' }}</td>
                                    <td>
                                        <x-status-badge domain="general" class="bg-success-subtle text-success " status="{{ $reg->status }}" set="bi" />

                                    </td>
                                    <td class="text-theme-muted fs-xs">{{ formatDate($reg->created_at) }}</td>
                                </tr>
                            @endforeach
                            @foreach ($teamRegistrations as $reg)
                                <tr>
                                    <td class="fw-bold fs-base">{{ $reg->competition->name }}</td>
                                    <td><span class="badge bg-primary-subtle text-primary fs-sm">{{ __('app.team') }}</span></td>
                                    <td class="fs-base">{{ $reg->team->name ?? '—' }}</td>
                                    <td>
                                        <x-status-badge domain="general" class="bg-success-subtle text-success " status="{{ $reg->status }}" set="bi" />

                                    </td>
                                    <td class="text-theme-muted fs-xs">{{ formatDate($reg->created_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
