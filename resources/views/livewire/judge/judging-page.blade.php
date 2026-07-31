<div>
    <x-section-header
        icon="bi-clipboard-data"
        :title="__('app.judge_competition')"
        :subtitle="$competition->name"
        :breadcrumbs="[
            ['label' => __('app.judging')],
            ['label' => $competition->name],
        ]"
    />

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @forelse($rounds as $round)
        <div class="card border-0 mb-4">
            <div class="card-header bg-transparent py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-layers me-1"></i>
                        {{ __('app.round', ['number' => $round->number]) }} — {{ $round->name }}
                    </h6>
                    @if ($round->starts_at || $round->ends_at)
                        <div class="text-chrome-muted fs-sm">
                            {{ $round->starts_at?->format('d/m/Y H:i') ?? '—' }} — {{ $round->ends_at?->format('d/m/Y H:i') ?? '—' }}
                        </div>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <x-status-badge domain="match" :status="$round->status" set="bi" />
                    <button
                        class="btn btn-sm {{ $round_id === $round->id ? 'btn-warning' : 'btn-outline-secondary' }} rounded-md"
                        wire:click="selectRound({{ $round->id }})">
                        <i class="bi bi-pencil-square"></i> {{ __('app.score_round') }}
                    </button>
                </div>
            </div>

            @if ($round_id === $round->id)
                <div class="card-body p-4">
                    @forelse($submissions as $item)
                        @php
                            $submission = $item->submission;
                        @endphp
                        <div class="border-bottom pb-3 mb-3" wire:key="submission-{{ $submission->id }}">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                                <div>
                                    <div class="fw-bold">{{ $submission->title }}</div>
                                    <div class="text-chrome-muted fs-sm">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $submission->getParticipantName() ?? '—' }}
                                    </div>
                                    @if ($submission->description)
                                        <div class="text-chrome-muted fs-sm mt-1">{{ $submission->description }}</div>
                                    @endif
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <x-status-badge domain="general" :status="$submission->status->value" set="bi" />
                                    @if (! $hideOtherJudges && $item->average !== null)
                                        <span class="fs-sm text-chrome-muted">
                                            <i class="bi bi-bar-chart me-1"></i>{{ __('app.average_score') }}: {{ $item->average }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <x-judge-score-input :submission-id="$submission->id" :max-score="$maxScore" />
                        </div>
                    @empty
                        <x-empty-state icon="bi-inbox" title="{{ __('app.no_submissions_for_round') }}" message="{{ __('app.no_submissions_for_round_message') }}" />
                    @endforelse
                </div>
            @endif
        </div>
    @empty
        <x-empty-state icon="bi-layers" title="{{ __('app.no_rounds_yet') }}" message="{{ __('app.no_rounds_yet_message') }}" />
    @endforelse
</div>
