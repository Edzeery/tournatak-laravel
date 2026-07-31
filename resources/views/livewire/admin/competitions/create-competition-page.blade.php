<div>
    <x-section-header
        :title="__('app.add_new_competition')"
        icon="bi-trophy-fill"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.competitions'), 'route' => route('admin.competitions.index')],
            ['label' => __('app.add_new')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.competitions.index') }}" class="btn btn-outline-secondary rounded-md">
                <i class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }}"></i> {{ __('app.back') }}
            </a>
        </x-slot:action>
    </x-section-header>

    {{-- Stepper --}}
    @if (count($this->steps()) > 1)
        <div class="card border-0 mb-4">
            <div class="card-body py-3">
                <ol class="d-flex flex-wrap align-items-center gap-2 list-unstyled mb-0">
                    @foreach ($this->steps() as $index => $stepKey)
                        <li>
                            <button type="button" wire:click="goToStep('{{ $stepKey }}')"
                                class="btn btn-sm rounded-pill {{ $index === $this->stepIndex() ? 'btn-warning fw-bold' : ($index < $this->stepIndex() ? 'btn-outline-warning' : 'btn-outline-secondary') }}">
                                <span class="badge rounded-circle {{ $index === $this->stepIndex() ? 'bg-dark' : 'bg-secondary' }} me-1">{{ $index + 1 }}</span>
                                {{ __("app.step_{$stepKey}") }}
                            </button>
                        </li>
                        @if (! $loop->last)
                            <li class="text-chrome-muted"><i class="bi bi-chevron-{{ isRtl() ? 'left' : 'right' }}"></i></li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>
    @endif

    <div class="card border-0">
        <div class="card-body p-4">
            @if ($this->step === 'domain')
                <h5 class="fw-bold mb-1">{{ __('app.select_domain_title') }}</h5>
                <p class="text-muted mb-4">{{ __('app.select_domain_desc') }}</p>
                <div class="row g-3">
                    @foreach ($this->domains() as $domain)
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <button type="button" wire:click="selectDomain({{ $domain->id }})"
                                class="w-100 h-100 text-start p-3 rounded-4 bg-body border {{ $this->domain_id === $domain->id ? 'border-warning border-2' : 'border-dark-subtle' }}">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="stat-icon bg-warning bg-opacity-10 text-gold">
                                        <i class="bi {{ $domain->icon }}"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">{{ $domain->localizedName() }}</h6>
                                </div>
                                <p class="text-muted small mb-0">{{ $domain->description }}</p>
                            </button>
                        </div>
                    @endforeach
                </div>
            @elseif ($this->step === 'review')
                <h5 class="fw-bold mb-1">{{ __('app.step_review') }}</h5>
                <p class="text-muted mb-4">{{ __('app.review_competition_desc') }}</p>
                <div class="row">
                    @foreach ($this->reviewItems() as $item)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded-3 p-3 h-100">
                                <small class="text-chrome-muted fw-bold text-uppercase d-block mb-1">{{ $item['label'] }}</small>
                                <span class="fw-bold">{{ $item['value'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-outline-secondary" wire:click="previousStep">
                        <i class="bi bi-arrow-{{ isRtl() ? 'right' : 'left' }} me-1"></i> {{ __('app.wizard_back') }}
                    </button>
                    <button type="button" class="btn btn-warning px-4" wire:click="store" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="store"><i class="bi bi-check-lg"></i> {{ __('app.save_competition') }}</span>
                        <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}...</span>
                    </button>
                </div>
            @else
                <h5 class="fw-bold mb-4">{{ __("app.step_{$this->step}") }}</h5>
                <form wire:submit="nextStep">
                    <div class="row">
                        @foreach ($this->stepFields() as $field)
                            @php
                                $wide = $field['type'] === 'textarea';
                                $placeholder = match ($field['name']) {
                                    'name' => __('app.enter_competition_name'),
                                    'location' => __('app.city_or_stadium'),
                                    'start_date', 'end_date' => __('app.select_date_time'),
                                    'description' => __('app.competition_desc_placeholder'),
                                    default => '',
                                };
                            @endphp
                            <div class="{{ $wide ? 'col-12' : 'col-md-6' }} mb-3">
                                <label class="form-label fw-bold">
                                    {{ $field['label'] }}
                                    @if ($field['required'])
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @if ($field['type'] === 'select')
                                    <select class="form-select" wire:model.live="{{ $field['name'] }}" @if ($field['required']) required @endif>
                                        <option value="">{{ $field['name'] === 'subtype_id' ? __('app.choose_subtype') : __('app.choose_type') }}</option>
                                        @foreach ($field['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @if ($field['name'] === 'type_id')
                                        <x-participant-type-badge :types="$this->types()" :type-id="$type_id" />
                                    @endif
                                @elseif ($field['type'] === 'textarea')
                                    <textarea class="form-control" wire:model="{{ $field['name'] }}" rows="3" @if ($placeholder) placeholder="{{ $placeholder }}" @endif></textarea>
                                @elseif ($field['type'] === 'datetime')
                                    <input type="text" class="form-control flatpickr-input" wire:model="{{ $field['name'] }}"
                                        placeholder="{{ __('app.select_date_time') }}"
                                        data-enable-time="true" data-date-format="Y-m-d H:i" data-alt-format="d/m/Y H:i">
                                @else
                                    <input type="text" class="form-control" wire:model="{{ $field['name'] }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-outline-secondary" wire:click="previousStep">
                            <i class="bi bi-arrow-{{ isRtl() ? 'right' : 'left' }} me-1"></i> {{ __('app.wizard_back') }}
                        </button>
                        <button type="submit" class="btn btn-warning px-4">
                            {{ __('app.wizard_continue') }} <i class="bi bi-arrow-{{ isRtl() ? 'left' : 'right' }} ms-1"></i>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
