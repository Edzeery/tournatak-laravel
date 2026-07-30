@props(['type' => null, 'types' => null, 'typeId' => null])

@php
    if ($typeId && $types) {
        $selectedType = $types->firstWhere('id', $typeId);
    } else {
        $selectedType = $type;
    }
@endphp

@if($selectedType)
    <div class="mt-2">
        <small class="text-muted">{{ __('app.participant_type') }}:</small>
        @if($selectedType->participant_type === 'individual')
            <span class="badge bg-info">{{ __('app.participant_type_individual') }}</span>
        @elseif($selectedType->participant_type === 'both')
            <span class="badge bg-warning">{{ __('app.participant_type_both') }}</span>
        @else
            <span class="badge bg-primary">{{ __('app.participant_type_team') }}</span>
        @endif
    </div>
@endif
