@props([
    'action' => '#',
    'id' => null,
    'class' => '',
    'label' => null,
    'icon' => 'bi-trash3',
])

@php
    $messageJs = addslashes(__('app.confirm_delete_message'));
    $titleJs = addslashes(__('app.confirm_delete_title'));
    $yesJs = addslashes(__('app.confirm_delete_yes'));
    $cancelJs = addslashes(__('app.confirm_delete_cancel'));
@endphp

<button
    type="button"
    {{ $attributes->merge(['class' => "btn btn-danger-outline " . $class]) }}
    onclick="confirmSweetAlert('{{ $action }}', '{{ $titleJs }}', '{{ $messageJs }}', '{{ $yesJs }}', '{{ $cancelJs }}')"
>
    @if($label)
        <i class="{{ $icon }} me-1"></i> {{ $label }}
    @else
        {{ $slot }}
    @endif
</button>


