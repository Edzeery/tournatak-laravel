@if($errors->any())
    <div {{ $attributes->merge(['class' => 'alert alert-danger d-flex align-items-center gap-2']) }} x-data="{}" x-init="if (typeof window.showToast === 'function') { window.showToast('error', @js(__('app.form_validation_failed'))); }">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
