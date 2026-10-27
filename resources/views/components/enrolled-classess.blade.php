<nav class="nav-group" aria-labelledby="enrolled-heading">
    <div class="nav-heading" id="enrolled-heading">Your classes</div>

    @forelse ($classess as $class)
        @php $isCurrent = (int) request()->route('class')?->id === $class->id; @endphp
        <a href="{{ route('stream', $class->id) }}" @class(['nav-link-item', 'active' => $isCurrent]) @if ($isCurrent) aria-current="page" @endif>
            <span class="avatar avatar-xs theme-{{ $class->themeIndex() }}" style="background: var(--banner); color: #fff" aria-hidden="true">{{ Str::upper(Str::substr($class->classname, 0, 1)) }}</span>
            <span class="nav-text">
                {{ $class->classname }}
                <small>{{ $class->section }}</small>
            </span>
        </a>
    @empty
        <p class="px-3 small text-muted-2 mb-0">Classes you create or join will show up here.</p>
    @endforelse
</nav>
