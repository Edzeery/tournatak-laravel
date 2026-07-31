<div>
    <x-section-header
        icon="bi-people"
        :title="__('app.manage_judging')"
        :subtitle="$competition->name"
        :breadcrumbs="[
            ['label' => __('app.dashboard'), 'route' => route('admin.dashboard')],
            ['label' => __('app.competitions'), 'route' => route('admin.competitions.index')],
            ['label' => $competition->name, 'route' => route('admin.competitions.edit', $competition)],
            ['label' => __('app.manage_judging')],
        ]"
    >
        <x-slot:action>
            <a href="{{ route('admin.competitions.edit', $competition) }}" class="btn btn-outline-secondary rounded-md">
                <i class="bi bi-arrow-right"></i> {{ __('app.back_button') }}
            </a>
        </x-slot:action>
    </x-section-header>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 mb-4">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-plus me-1"></i> {{ __('app.assign_judge') }}</h6>
                </div>
                <div class="card-body p-4">
                    <form wire:submit="addJudge">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.judge_user') }}</label>
                            <select class="form-select" wire:model="newJudgeUserId" required>
                                <option value="">—</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('newJudgeUserId') <div class="text-danger fs-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" wire:model="newJudgeLead" id="newJudgeLead">
                            <label class="form-check-label" for="newJudgeLead">{{ __('app.lead_judge') }}</label>
                        </div>
                        <button type="submit" class="btn btn-warning w-100" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="addJudge"><i class="bi bi-plus-lg"></i> {{ __('app.add_judge') }}</span>
                            <span wire:loading wire:target="addJudge"><span class="spinner-border spinner-border-sm"></span> {{ __('app.saving') }}</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 mb-4">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-people me-1"></i> {{ __('app.judges') }}</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($judges as $judge)
                            <li class="list-group-item d-flex align-items-center justify-content-between gap-2" wire:key="judge-{{ $judge->id }}">
                                <div>
                                    <span class="fw-bold">{{ $judge->user?->name ?? '—' }}</span>
                                    @if ($judge->isLead())
                                        <span class="badge bg-warning ms-1">{{ __('app.lead_judge') }}</span>
                                    @endif
                                </div>
                                <button class="btn btn-sm btn-outline-danger rounded-md" wire:click="removeJudge({{ $judge->id }})" aria-label="{{ __('app.remove_judge') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </li>
                        @empty
                            <li class="list-group-item">
                                <x-empty-state icon="bi-people" title="{{ __('app.no_judges_yet') }}" message="{{ __('app.no_judges_yet_message') }}" />
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="card border-0">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-sliders me-1"></i> {{ __('app.judging_settings') }}</h6>
                </div>
                <div class="card-body p-4">
                    <form wire:submit="saveSettings">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" wire:model="hideOtherJudges" id="hideOtherJudges">
                            <label class="form-check-label" for="hideOtherJudges">{{ __('app.hide_other_judges') }}</label>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100 rounded-md">
                            <i class="bi bi-check-lg"></i> {{ __('app.save_settings') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0">
                <div class="card-header bg-transparent py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart me-1 text-gold"></i> {{ __('app.results_ranking') }}</h6>
                    <span class="badge badge-sport">{{ __('app.aggregation_' . $aggregation) }}</span>
                </div>
                <div class="card-body p-0">
                    @php
                        $hasScores = collect($ranking)->contains(fn ($row) => $row['scores_count'] > 0);
                    @endphp
                    @if ($hasScores)
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('app.participant') }}</th>
                                        <th class="text-center">{{ __('app.score') }}</th>
                                        <th class="text-center">{{ __('app.judges') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ranking as $idx => $row)
                                        <tr class="{{ $idx < 3 ? 'table-highlight' : '' }}" wire:key="rank-{{ $row['submission_id'] }}">
                                            <td class="fw-bold">{{ $idx + 1 }}</td>
                                            <td class="fw-bold">{{ $row['participant_name'] ?? '—' }}</td>
                                            <td class="text-center fw-bold fs-base text-gold">{{ number_format($row['score'], 2) }} / {{ number_format($maxScore, 2) }}</td>
                                            <td class="text-center text-chrome-muted">{{ $row['scores_count'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4">
                            <x-empty-state icon="bi-bar-chart" title="{{ __('app.results_not_available') }}" message="{{ __('app.results_will_appear_once_scored') }}" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
