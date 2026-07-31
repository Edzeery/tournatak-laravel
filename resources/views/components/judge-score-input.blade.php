@props([
    'submissionId' => null,
    'maxScore' => 100,
    'label' => null,
])

<div class="judge-score-input">
    <div class="row g-2 align-items-end">
        <div class="col-12 col-sm-3">
            <label class="form-label fw-bold fs-sm mb-1">
                {{ $label ?? __('app.score') }} <span class="text-danger">*</span>
            </label>
            <input
                type="number"
                class="form-control"
                min="0"
                max="{{ $maxScore }}"
                step="0.5"
                wire:model="scores.{{ $submissionId }}.score"
                placeholder="0"
                aria-label="{{ __('app.score') }}"
            >
        </div>
        <div class="col-12 col-sm-6">
            <label class="form-label fw-bold fs-sm mb-1">{{ __('app.notes') }}</label>
            <input
                type="text"
                class="form-control"
                wire:model="scores.{{ $submissionId }}.notes"
                placeholder="{{ __('app.optional') }}"
                aria-label="{{ __('app.notes') }}"
            >
        </div>
        <div class="col-12 col-sm-3">
            <button type="button" class="btn btn-warning w-100" wire:click="saveScore({{ $submissionId }})" wire:loading.attr="disabled">
                <i class="bi bi-check-lg"></i> {{ __('app.save_score') }}
            </button>
        </div>
    </div>
    @error("scores.{$submissionId}.score")
        <div class="text-danger fs-sm mt-1">{{ $message }}</div>
    @enderror
</div>
