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

@once
@push('scripts')
<script>
    function confirmSweetAlert(url, title, message, confirmText, cancelText) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            reverseButtons: true,
            background: '#1a1f35',
            color: '#fff',
            borderColor: 'rgba(255,193,7,0.15)',
            customClass: {
                popup: 'swal-tournatak',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.Livewire.navigate(url);
            }
        });
    }
</script>
@endpush
@endonce
