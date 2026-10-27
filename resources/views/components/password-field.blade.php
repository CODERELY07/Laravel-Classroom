@props(['name', 'label', 'autocomplete' => 'current-password', 'hint' => null])
@php $id = $name.'-'.Str::random(4); @endphp
<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <div class="password-field">
        <input id="{{ $id }}" type="password" name="{{ $name }}" autocomplete="{{ $autocomplete }}" {{ $attributes->merge(['class' => 'form-control']) }}>
        <button type="button" class="icon-btn toggle-visibility" data-toggle-password="#{{ $id }}" aria-label="Show password" aria-pressed="false">
            <i class="fa-regular fa-eye" aria-hidden="true"></i>
        </button>
    </div>
    @if ($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
    <div class="invalid-feedback d-block" data-error-for="{{ $name }}"></div>
</div>
