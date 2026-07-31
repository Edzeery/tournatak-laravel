<div>
    <x-section-header
        icon="bi bi-grid-1x2-fill"
        :title="__('app.page_title_manage_domains')"
        :subtitle="__('app.domains_desc')"
        :breadcrumbs="[
            ['route' => route('admin.dashboard'), 'label' => __('app.dashboard')],
            ['label' => __('app.domains')],
        ]"
    />

    <div class="card border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('app.domain') }}</th>
                            <th>{{ __('app.slug') }}</th>
                            <th>{{ __('app.evaluation_basis') }}</th>
                            <th>{{ __('app.participant_basis') }}</th>
                            <th>{{ __('app.competitions_count') }}</th>
                            <th>{{ __('app.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($domains as $domain)
                            <tr wire:key="{{ $domain->id }}">
                                <td class="text-chrome-muted">{{ $domain->sort_order }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi {{ $domain->icon }} text-gold"></i>
                                        <span class="fw-bold">{{ $domain->localizedName() }}</span>
                                    </div>
                                </td>
                                <td><code class="rounded-xs badge-type-gold">{{ $domain->slug }}</code></td>
                                <td>
                                    @if ($domain->usesSubmissionEvaluation())
                                        <span class="badge bg-info">{{ __('app.evaluation_basis_submission') }}</span>
                                    @else
                                        <span class="badge bg-primary">{{ __('app.evaluation_basis_match') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($domain->participant_basis === \App\Models\CompetitionDomain::PARTICIPANT_INDIVIDUAL)
                                        <span class="badge bg-info">{{ __('app.participant_type_individual') }}</span>
                                    @elseif ($domain->participant_basis === \App\Models\CompetitionDomain::PARTICIPANT_BOTH)
                                        <span class="badge bg-warning">{{ __('app.participant_type_both') }}</span>
                                    @else
                                        <span class="badge bg-primary">{{ __('app.participant_type_team') }}</span>
                                    @endif
                                </td>
                                <td>{{ $domain->competitions_count }}</td>
                                <td>
                                    @if ($domain->is_active)
                                        <x-status-badge domain="competition" status="active" set="bi" />
                                    @else
                                        <x-status-badge domain="competition" status="inactive" set="bi" />
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <x-empty-state icon="bi-grid-1x2" title="{{ __('app.domains') }}" message="{{ __('app.no_results_found') }}" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
