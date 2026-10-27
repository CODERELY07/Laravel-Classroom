@props(['icon' => 'fa-regular fa-folder-open', 'title'])
<div {{ $attributes->class('empty-state') }}>
    <div class="empty-icon" aria-hidden="true"><i class="{{ $icon }}"></i></div>
    <h2>{{ $title }}</h2>
    {{ $slot }}
</div>
