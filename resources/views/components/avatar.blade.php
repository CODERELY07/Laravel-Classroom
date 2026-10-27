@props(['user', 'size' => null])
{{-- Profile photo, or the first letter of the name when there is none. --}}
<span {{ $attributes->class(['avatar', 'avatar-'.$size => $size]) }}>
    @if ($user?->avatar_url)
        <img src="{{ $user->avatar_url }}" alt="" loading="lazy" decoding="async">
    @else
        <span aria-hidden="true">{{ $user?->initial }}</span>
    @endif
</span>
