<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-white fw-bold mb-1"><i class="bi bi-trash3 text-gold me-2"></i>{{ __('app.trash') }}</h4>
            <p class="mb-0 fs-md text-faded-40">{{ __('app.trash_description') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4 card-dark rounded-xl">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach([
                            'all' => __('app.all'),
                            'teams' => __('app.teams'),
                            'players' => __('app.players'),
                            'competitions' => __('app.competitions'),
                            'matches' => __('app.matches'),
                            'users' => __('app.users'),
                        ] as $key => $label)
                            <button
                                type="button"
                                class="btn btn-sm {{ $filterType === $key ? 'btn-gold' : 'lang-btn-inactive' }} rounded-md"
                                wire:click="filterType = '{{ $key }}'"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute input-icon-pos text-faded-30"></i>
                        <input
                            type="text"
                            class="form-control card-dark rounded-lg-custom input-icon-right"
                            placeholder="{{ __('app.search') }}..."
                            wire:model.live.debounce.300ms="search"
                            aria-label="{{ __('app.search') }}"
                        />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-end fs-base text-faded-40">
                        @if(method_exists($records, 'total'))
                            {{ $records->total() }} {{ __('app.results') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Records Table --}}
    <div class="card card-dark rounded-xl">
        <div class="card-body p-0">
            @if($records->isEmpty())
                <x-empty-state icon="bi-trash3" :title="__('app.no_records')" :message="__('app.trash_empty_message')" />
            @else
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr class="border-chrome-bottom">
                                <th class="fs-sm th-dark">{{ __('app.name') }}</th>
                                <th class="fs-sm th-dark">{{ __('app.type') }}</th>
                                <th class="fs-sm th-dark">{{ __('app.deleted_at') }}</th>
                                <th class="fs-sm th-dark text-end">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr class="border-row-chrome">
                                    <td class="cell-pad-lg text-white fw-bold">
                                        {{ $record->name ?? $record->username ?? '#' . $record->id }}
                                    </td>
                                    <td class="cell-pad-lg">
                                        @php
                                            $typeLabels = [
                                                'App\Models\Team' => __('app.teams'),
                                                'App\Models\Player' => __('app.players'),
                                                'App\Models\Competition' => __('app.competitions'),
                                                'App\Models\Match_' => __('app.matches'),
                                                'App\Models\User' => __('app.users'),
                                            ];
                                            $typeName = class_basename($record);
                                            $typeKeys = [
                                                'Team' => 'teams',
                                                'Player' => 'players',
                                                'Competition' => 'competitions',
                                                'Match_' => 'matches',
                                                'User' => 'users',
                                            ];
                                        @endphp
                                        <span class="badge rounded-sm-custom fs-sm badge-type-gold">
                                            {{ $typeLabels[get_class($record)] ?? $typeName }}
                                        </span>
                                    </td>
                                    <td class="fs-md cell-pad-lg text-faded-50">
                                        {{ $record->deleted_at->diffForHumans() }}
                                    </td>
                                    <td class="cell-pad-lg text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button
                                                class="btn btn-sm btn-outline-success rounded-md"
                                                x-on:click.prevent="confirmAction({ title: @js(__('app.restore')), text: @js(__('app.restore_confirm')), icon: 'info', confirmButtonText: @js(__('app.restore')), cancelButtonText: @js(__('app.confirm_delete_cancel')) }).then(ok => ok && $wire.restore(@js($typeKeys[$typeName]), {{ $record->id }}))"
                                                title="{{ __('app.restore') }}"
                                                aria-label="{{ __('app.restore') }}"
                                            >
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-outline-danger rounded-md"
                                                x-on:click.prevent="confirmAction({ title: @js(__('app.confirm_delete_title')), text: @js(__('app.force_delete_confirm')), icon: 'warning', confirmButtonText: @js(__('app.confirm_delete_yes')), cancelButtonText: @js(__('app.confirm_delete_cancel')) }).then(ok => ok && $wire.forceDelete(@js($typeKeys[$typeName]), {{ $record->id }}))"
                                                title="{{ __('app.force_delete') }}"
                                                aria-label="{{ __('app.force_delete') }}"
                                            >
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($records, 'links'))
                    <div class="p-3">
                        {{ $records->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
