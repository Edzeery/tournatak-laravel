<div>
    <x-section-header
        :title="__('app.edit_competition')"
        icon="bi-pencil"
        :subtitle="$competition->name"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.competitions'), 'route' => route('admin.competitions.index')],
            ['label' => __('app.edit_competition')],
        ]"
    >
        <x-slot:action>
            @if ($competition->usesSubmissionEvaluation())
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.competitions.rounds', $competition) }}" class="btn btn-outline-secondary rounded-md">
                        <i class="bi bi-layers"></i> {{ __('app.manage_rounds') }}
                    </a>
                    <a href="{{ route('admin.competitions.submissions', $competition) }}" class="btn btn-outline-secondary rounded-md">
                        <i class="bi bi-clipboard-check"></i> {{ __('app.manage_submissions') }}
                    </a>
                    <a href="{{ route('admin.competitions.judging', $competition) }}" class="btn btn-outline-secondary rounded-md">
                        <i class="bi bi-people"></i> {{ __('app.manage_judging') }}
                    </a>
                </div>
            @endif
            <a href="{{ route('admin.competitions.index') }}" class="btn btn-outline-secondary rounded-md">
                <i class="bi bi-arrow-right"></i> {{ __('app.back_button') }}
            </a>
        </x-slot:action>
    </x-section-header>

    @if ($competition->domain)
        <div class="mb-3">
            <span class="badge badge-sport fs-base">
                <i class="bi {{ $competition->domain->icon }} me-1"></i>
                {{ $competition->domain->localizedName() }}
            </span>
            @if ($competition->usesSubmissionEvaluation())
                <span class="text-chrome-muted fs-sm ms-2">
                    <i class="bi bi-info-circle me-1"></i>
                    {{ __('app.submission_domain_manage_hint') }}
                </span>
            @endif
        </div>
    @endif

    <div class="card border-0">
        <div class="card-body p-4">
            <form wire:submit="update">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.competition_name_label') }}</label>
                        <input type="text" class="form-control" wire:model="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.competition_type_label') }}</label>
                        <select class="form-select" wire:model.live="type_id" required>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <x-participant-type-badge :types="$types" :type-id="$type_id" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.subtype_label') }}</label>
                        <select class="form-select" wire:model="subtype_id" required>
                            @foreach($subtypes as $subtype)
                                <option value="{{ $subtype->id }}" {{ $subtype->id == $competition->subtype_id ? 'selected' : '' }}>{{ $subtype->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.status') }}</label>
                        <select class="form-select" wire:model="status">
                            <option value="draft">{{ __('app.draft') }}</option>
                            <option value="upcoming">{{ __('app.upcoming') }}</option>
                            <option value="ongoing">{{ __('app.ongoing') }}</option>
                            <option value="completed">{{ __('app.completed_status') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.approval_label') }}</label>
                        <select class="form-select" wire:model="approval_status">
                            <option value="pending">{{ __('app.pending_review') }}</option>
                            <option value="approved">{{ __('app.approved_status') }}</option>
                            <option value="rejected">{{ __('app.rejected_status') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.location_label') }}</label>
                        <input type="text" class="form-control" wire:model="location">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.start_date_label') }}</label>
                        <input type="text" class="form-control flatpickr-input" wire:model="start_date" placeholder="{{ __('app.select_date_time') }}" data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.end_date_label') }}</label>
                        <input type="text" class="form-control flatpickr-input" wire:model="end_date" placeholder="{{ __('app.select_date_time') }}" data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">{{ __('app.description_label') }}</label>
                        <textarea class="form-control" wire:model="description" rows="3"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="update"><i class="bi bi-check-lg"></i> {{ __('app.save_changes') }}</span>
                    <span wire:loading wire:target="update"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>
