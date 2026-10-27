@props([
    'name',
    'label',
    'type' => 'text',
    'id' => null,
    'value' => null,
    'hint' => null,
])
{{-- Label + control + slot for server-side validation messages (filled by forms.js). --}}
@php $id = $id ?? str_replace(['[', ']'], ['-', ''], $name).'-'.Str::random(4); @endphp
<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'form-control', 'rows' => 4]) }}>{{ $value }}</textarea>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" {{ $attributes->merge(['class' => 'form-control']) }}>
    @endif
    @if ($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
    <div class="invalid-feedback" data-error-for="{{ $name }}"></div>
</div>
