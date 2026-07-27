<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-white fw-bold mb-1"><i class="bi bi-trash3 text-gold me-2"></i>{{ __('app.trash') }}</h4>
            <p style="color:rgba(255,255,255,0.4);font-size:0.9rem;" class="mb-0">{{ __('app.trash_description') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:16px;">
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
                                class="btn btn-sm {{ $filterType === $key ? 'btn-gold' : '' }}"
                                style="border-radius:8px;{{ $filterType !== $key ? 'background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.5);border:1px solid rgba(255,255,255,0.08);' : '' }}"
                                wire:click="filterType = '{{ $key }}'"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute" style="right:12px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);"></i>
                        <input
                            type="text"
                            class="form-control"
                            style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#fff;border-radius:10px;padding-right:38px;"
                            placeholder="{{ __('app.search') }}..."
                            wire:model.live.debounce.300ms="search"
                        />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-end" style="color:rgba(255,255,255,0.4);font-size:0.85rem;">
                        @if(method_exists($records, 'total'))
                            {{ $records->total() }} {{ __('app.results') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Records Table --}}
    <div class="card" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:16px;">
        <div class="card-body p-0">
            @if($records->isEmpty())
                <x-empty-state icon="bi-trash3" :title="__('app.no_records')" :message="__('app.trash_empty_message')" />
            @else
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.06);">
                                <th style="color:rgba(255,255,255,0.5);font-size:0.8rem;text-transform:uppercase;padding:14px 20px;">{{ __('app.name') }}</th>
                                <th style="color:rgba(255,255,255,0.5);font-size:0.8rem;text-transform:uppercase;padding:14px 20px;">{{ __('app.type') }}</th>
                                <th style="color:rgba(255,255,255,0.5);font-size:0.8rem;text-transform:uppercase;padding:14px 20px;">{{ __('app.deleted_at') }}</th>
                                <th style="color:rgba(255,255,255,0.5);font-size:0.8rem;text-transform:uppercase;padding:14px 20px;text-align:end;">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td style="padding:14px 20px;color:#fff;font-weight:600;">
                                        {{ $record->name ?? $record->username ?? '#' . $record->id }}
                                    </td>
                                    <td style="padding:14px 20px;">
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
                                        <span class="badge" style="background:rgba(255,193,7,0.12);color:#ffc107;border-radius:6px;padding:4px 10px;font-size:0.75rem;">
                                            {{ $typeLabels[get_class($record)] ?? $typeName }}
                                        </span>
                                    </td>
                                    <td style="padding:14px 20px;color:rgba(255,255,255,0.5);font-size:0.9rem;">
                                        {{ $record->deleted_at->diffForHumans() }}
                                    </td>
                                    <td style="padding:14px 20px;text-align:end;">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button
                                                class="btn btn-sm btn-outline-success"
                                                style="border-radius:8px;"
                                                wire:click="restore('{{ $typeKeys[$typeName] }}', {{ $record->id }})"
                                                wire:confirm="{{ __('app.restore_confirm') }}"
                                                title="{{ __('app.restore') }}"
                                            >
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-outline-danger"
                                                style="border-radius:8px;"
                                                wire:click="forceDelete('{{ $typeKeys[$typeName] }}', {{ $record->id }})"
                                                wire:confirm="{{ __('app.force_delete_confirm') }}"
                                                title="{{ __('app.force_delete') }}"
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
