@props([
    'name' => 'date',
    'value' => null,
    'label' => null,
    'required' => false,
    'enableTime' => false,
    'dateFormat' => 'Y-m-d',
    'altFormat' => 'd/m/Y',
    'placeholder' => null,
    'error' => null,
    'inline' => false,
    'wireModel' => null,
])

<div class="date-picker-wrapper">
    @if($label)
        <label for="{{ $name }}" class="form-label fw-semibold mb-1">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    <input
        type="text"
        class="form-control flatpickr-input @if($error) is-invalid @endif"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder ?? ($enableTime ? __('app.select_date_time') : __('app.select_date')) }}"
        @if($required) required @endif
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        data-enable-time="{{ $enableTime ? 'true' : 'false' }}"
        data-date-format="{{ $dateFormat }}"
        data-alt-format="{{ $altFormat }}"
        @if($inline) style="background: transparent;" @endif
    />
    @if($error)
        <div class="invalid-feedback">{{ $error }}</div>
    @endif
</div>
