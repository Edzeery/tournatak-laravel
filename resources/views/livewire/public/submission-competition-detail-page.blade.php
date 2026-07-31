<div>
    {{-- Hero --}}
    <section class="hero-sports hero-sports-sm text-white position-relative overflow-hidden">
        <div class="hero-gradient-bottom"></div>
        <div class="container hero-content">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="hero-badge d-inline-flex mb-3">
                        @if ($competition->domain)
                            <i class="bi {{ $competition->domain->icon }} me-1"></i>
                            {{ $competition->domain->localizedName() }}
                        @else
                            <i class="bi bi-trophy-fill me-1"></i>
                            {{ $competition->type?->name ?? __('app.competition') }}
                        @endif
                    </div>
                    <h1 class="fw-bold mb-2 fs-4xl">{{ $competition->name }}</h1>
                    <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
                        <x-status-badge domain="competition" status="{{ $competition->status }}" set="bi" />
                        <span class="badge badge-sport">
                            <i class="bi bi-clipboard-data me-1"></i>
                            {{ __('app.evaluation_basis_submission') }}
                        </span>
                        <span class="text-chrome-muted fs-sm">
                            <i class="bi bi-calendar-event text-gold me-1"></i>
                            {{ $competition->start_date?->format('d/m/Y') ?? '—' }} — {{ $competition->end_date?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hero-stats-panel">
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="hero-stat-item">
                                    <div class="stat-number">{{ $competition->rounds->count() }}</div>
                                    <div class="stat-label">{{ __('app.rounds') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="hero-stat-item">
                                    <div class="stat-number">{{ $competition->submissions->count() }}</div>
                                    <div class="stat-label">{{ __('app.submissions') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="hero-stat-item">
                                    <div class="stat-number">{{ $competition->judges->count() }}</div>
                                    <div class="stat-label">{{ __('app.judges') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <div class="container py-5 mt-neg-20">
        {{-- Tabs --}}
        <ul class="nav nav-tabs-custom mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview-tab" type="button" role="tab">
                    <i class="bi bi-info-circle me-1"></i> {{ __('app.overview') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rounds-submissions-tab" type="button" role="tab">
                    <i class="bi bi-list-check me-1"></i> {{ __('app.rounds_submissions') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#results-tab" type="button" role="tab">
                    <i class="bi bi-bar-chart me-1"></i> {{ __('app.results') }}
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Overview Tab --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">{{ __('app.overview') }}</h5>
                                <p class="text-chrome-muted mb-0">{{ $competition->description ?: __('app.no_description_yet') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">{{ __('app.competition_details') }}</h5>
                                <ul class="list-unstyled mb-0 d-grid gap-3">
                                    <li>
                                        <span class="text-chrome-muted d-block fs-sm">{{ __('app.domain') }}</span>
                                        @if ($competition->domain)
                                            <span class="badge badge-domain mt-1">
                                                <i class="bi {{ $competition->domain->icon }} me-1"></i>
                                                {{ $competition->domain->localizedName() }}
                                            </span>
                                        @else
                                            <span class="fw-bold">—</span>
                                        @endif
                                    </li>
                                    <li>
                                        <span class="text-chrome-muted d-block fs-sm">{{ __('app.evaluation_basis') }}</span>
                                        <span class="fw-bold">{{ __('app.evaluation_basis_submission') }}</span>
                                    </li>
                                    <li>
                                        <span class="text-chrome-muted d-block fs-sm">{{ __('app.participant_type') }}</span>
                                        <span class="fw-bold">
                                            @php
                                                $basis = $competition->domain?->participant_basis ?? 'team';
                                            @endphp
                                            {{ match ($basis) {
                                                'individual' => __('app.participant_type_individual'),
                                                'both' => __('app.participant_type_both'),
                                                default => __('app.participant_type_team'),
                                            } }}
                                        </span>
                                    </li>
                                    <li>
                                        <span class="text-chrome-muted d-block fs-sm">{{ __('app.location') }}</span>
                                        <span class="fw-bold">{{ $competition->location ?: '—' }}</span>
                                    </li>
                                    <li>
                                        <span class="text-chrome-muted d-block fs-sm">{{ __('app.organizer') }}</span>
                                        <span class="fw-bold">{{ $competition->organizer?->name ?: '—' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rounds & Submissions Tab --}}
            <div class="tab-pane fade" id="rounds-submissions-tab" role="tabpanel">
                @forelse($competition->rounds as $round)
                    @php
                        $roundSubmissions = $competition->submissions->where('round_id', $round->id);
                    @endphp
                    <div class="card border-0 shadow-sm mb-4" wire:key="round-{{ $round->id }}">
                        <div class="card-header bg-transparent d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    <i class="bi bi-layers me-1"></i>
                                    {{ __('app.round', ['number' => $round->number]) }} — {{ $round->name }}
                                </h6>
                                <div class="text-chrome-muted fs-sm">
                                    @if ($round->starts_at || $round->ends_at)
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $round->starts_at?->format('d/m/Y H:i') ?? '—' }} — {{ $round->ends_at?->format('d/m/Y H:i') ?? '—' }}
                                    @endif
                                </div>
                            </div>
                            <x-status-badge domain="match" :status="$round->status" set="bi" />
                        </div>
                        <div class="card-body p-4">
                            @forelse($roundSubmissions as $submission)
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 border-bottom pb-3 mb-3">
                                    <div>
                                        <div class="fw-bold">{{ $submission->title }}</div>
                                        <div class="text-chrome-muted fs-sm">
                                            <i class="bi bi-person me-1"></i>
                                            {{ $submission->getParticipantName() ?? '—' }}
                                        </div>
                                        @if ($submission->description)
                                            <div class="text-chrome-muted fs-sm mt-1">{{ Str::limit($submission->description, 160) }}</div>
                                        @endif
                                    </div>
                                    <x-status-badge domain="general" :status="$submission->status->value" set="bi" />
                                </div>
                            @empty
                                <div class="text-chrome-muted fs-sm"><i class="bi bi-inbox me-1"></i>{{ __('app.no_submissions_for_round') }}</div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <x-empty-state icon="bi-layers" title="{{ __('app.no_rounds_yet') }}" message="{{ __('app.no_rounds_yet_message') }}" />
                @endforelse
            </div>

            {{-- Results Tab --}}
            <div class="tab-pane fade" id="results-tab" role="tabpanel">
                @php
                    $hasScores = collect($ranking)->contains(fn ($row) => $row['scores_count'] > 0);
                @endphp
                @if ($hasScores)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-trophy me-1 text-gold"></i>
                                {{ __('app.ranking') }}
                            </h6>
                            <span class="badge badge-sport">{{ __('app.aggregation_' . $aggregation) }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table standings-table mb-0">
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
                                        <tr class="{{ $idx < 3 ? 'table-highlight' : '' }}">
                                            <td class="fw-bold">{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="team-standings-dot {{ ['rank-gold', 'rank-silver', 'rank-bronze', ''][$idx] ?? '' }}"></span>
                                                    {{ $row['participant_name'] ?? '—' }}
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold fs-base text-gold">
                                                {{ number_format($row['score'], 2) }} / {{ number_format($maxScore, 2) }}
                                            </td>
                                            <td class="text-center text-chrome-muted">{{ $row['scores_count'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <x-empty-state icon="bi-bar-chart" title="{{ __('app.results_not_available') }}" message="{{ __('app.results_will_appear_once_scored') }}" />
                @endif
            </div>
        </div>
    </div>
</div>
