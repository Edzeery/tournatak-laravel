<div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color:var(--primary);">{{ __('app.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}" class="text-decoration-none" style="color:var(--primary);">{{ __('app.teams') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.teams.edit', $team) }}" class="text-decoration-none" style="color:var(--primary);">{{ $team->name }}</a></li>
            <li class="breadcrumb-item active">{{ __('app.medical_record') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--dark);"><i class="bi bi-heart-pulse-fill text-gold"></i> {{ __('app.medical_record') }}</h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;">{{ $team->name }}</p>
        </div>
        <button class="btn btn-warning" wire:click="openModal">
            <i class="bi bi-plus-lg"></i> {{ __('app.add_medical_record') }}
        </button>
    </div>

    <div class="row g-3 mb-4">
        @php
            $activeCount = collect($medicalRecords)->where('status', 'active')->count();
            $recoveringCount = collect($medicalRecords)->where('status', 'recovering')->count();
            $returnedCount = collect($medicalRecords)->where('status', 'returned')->count();
            $longTermCount = collect($medicalRecords)->where('status', 'long_term')->count();
        @endphp
        <div class="col-md-3 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <div class="fw-bold" style="font-size:1.8rem;color:#dc3545;">{{ $activeCount }}</div>
                    <small class="text-muted fw-bold">{{ __('app.status_active') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <div class="fw-bold" style="font-size:1.8rem;color:#ffc107;">{{ $recoveringCount }}</div>
                    <small class="text-muted fw-bold">{{ __('app.status_recovering') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <div class="fw-bold" style="font-size:1.8rem;color:#198754;">{{ $returnedCount }}</div>
                    <small class="text-muted fw-bold">{{ __('app.status_returned') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body py-3">
                    <div class="fw-bold" style="font-size:1.8rem;color:#6f42c1;">{{ $longTermCount }}</div>
                    <small class="text-muted fw-bold">{{ __('app.status_long_term') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('app.search') }}" wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">{{ __('app.filter_by_status') }}</label>
                    <select class="form-select" wire:model.live="filterStatus" aria-label="{{ __('app.search') }}">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">{{ __('app.filter_by_type') }}</label>
                    <select class="form-select" wire:model.live="filterType" aria-label="{{ __('app.search') }}">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($recordTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0" wire:loading.opacity>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('app.player_label') }}</th>
                            <th>{{ __('app.record_type') }}</th>
                            <th>{{ __('app.injury_name') }}</th>
                            <th>{{ __('app.severity') }}</th>
                            <th>{{ __('app.medical_status') }}</th>
                            <th>{{ __('app.injury_date') }}</th>
                            <th>{{ __('app.expected_return') }}</th>
                            <th class="text-center">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicalRecords as $record)
                            <tr wire:key="{{ $record->id }}">
                                <td class="fw-bold">{{ $record->player->user->name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark fw-bold" style="font-size:0.75rem;">
                                        {{ $recordTypes[$record->record_type] ?? $record->record_type }}
                                    </span>
                                </td>
                                <td>{{ $record->injury_name ?? '—' }}</td>
                                <td>
                                    @php
                                        $severityColors = [
                                            'minor' => 'success',
                                            'moderate' => 'warning',
                                            'severe' => 'warning',
                                            'critical' => 'danger',
                                        ];
                                        $severityStyles = [
                                            'minor' => '',
                                            'moderate' => '',
                                            'severe' => 'background-color:#fd7e14',
                                            'critical' => '',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $severityColors[$record->severity] ?? 'secondary' }} fw-bold" style="font-size:0.75rem;{{ $severityStyles[$record->severity] ?? '' }}">
                                        {{ $severityLevels[$record->severity] ?? $record->severity }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'active' => 'danger',
                                            'recovering' => 'warning',
                                            'returned' => 'success',
                                            'long_term' => 'secondary',
                                        ];
                                        $statusStyles = [
                                            'active' => '',
                                            'recovering' => '',
                                            'returned' => '',
                                            'long_term' => 'background-color:#6f42c1',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$record->status] ?? 'secondary' }} fw-bold" style="font-size:0.75rem;{{ $statusStyles[$record->status] ?? '' }}">
                                        {{ $statusOptions[$record->status] ?? $record->status }}
                                    </span>
                                </td>
                                <td style="font-size:0.85rem;color:#94a3b8;">
                                    {{ formatDate($record->injury_date) ?? '—' }}
                                </td>
                                <td style="font-size:0.85rem;color:#94a3b8;">
                                    {{ formatDate($record->expected_return) ?? '—' }}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" style="border-radius:8px;" wire:click="editRecord({{ $record->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;"
                                            wire:click="deleteRecord({{ $record->id }})"
                                            wire:confirm="{{ __('app.confirm_delete_medical') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state py-3">
                                        <i class="bi bi-heart-pulse d-block" style="font-size:2.5rem;"></i>
                                        <h5>{{ __('app.no_medical_records') }}</h5>
                                        <p class="text-muted">{{ __('app.no_medical_records_desc') }}</p>
                                        <button class="btn btn-warning" wire:click="openModal">
                                            <i class="bi bi-plus-lg"></i> {{ __('app.add_medical_record') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="medicalModalTitle" style="background:rgba(0,0,0,0.5);" wire:click.self="closeModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="border-radius:16px;" wire:click.stop>
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="medicalModalTitle">
                            <i class="bi bi-heart-pulse-fill text-gold"></i>
                            {{ $editingRecordId ? __('app.edit_medical_record') : __('app.add_medical_record') }}
                        </h5>
                        <button type="button" class="btn-close" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.player_label') }}</label>
                                <select class="form-select" wire:model="recordForm.player_id">
                                    <option value="">{{ __('app.choose_player_medical') }}</option>
                                    @foreach($players as $player)
                                        <option value="{{ $player->id }}">{{ $player->user->name ?? '—' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.record_type') }}</label>
                                <select class="form-select" wire:model="recordForm.record_type">
                                    @foreach($recordTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.injury_name') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('app.injury_name_placeholder') }}" wire:model="recordForm.injury_name">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.severity') }}</label>
                                <select class="form-select" wire:model="recordForm.severity">
                                    @foreach($severityLevels as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.medical_status') }}</label>
                                <select class="form-select" wire:model="recordForm.status">
                                    @foreach($statusOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.injury_date') }}</label>
                                <input type="text" class="form-control flatpickr-input" wire:model="recordForm.injury_date" placeholder="{{ __('app.select_date') }}" data-date-format="Y-m-d" data-alt-format="d/m/Y">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('app.expected_return') }}</label>
                                <input type="text" class="form-control flatpickr-input" wire:model="recordForm.expected_return" placeholder="{{ __('app.select_date') }}" data-date-format="Y-m-d" data-alt-format="d/m/Y">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.treatment') }}</label>
                            <textarea class="form-control" rows="2" placeholder="{{ __('app.treatment_placeholder') }}" wire:model="recordForm.treatment"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.tactic_notes') }}</label>
                            <textarea class="form-control" rows="2" placeholder="{{ __('app.notes_placeholder') }}" wire:model="recordForm.notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" style="border-radius:8px;">{{ __('app.cancel') }}</button>
                        <button type="button" class="btn btn-warning px-4" wire:click="saveRecord" wire:loading.attr="disabled" style="border-radius:8px;">
                            <span wire:loading.remove wire:target="saveRecord"><i class="bi bi-check-lg"></i> {{ __('app.save') }}</span>
                            <span wire:loading wire:target="saveRecord"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
