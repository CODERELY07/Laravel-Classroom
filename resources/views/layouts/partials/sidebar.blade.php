<aside id="sidebar" class="sidebar" aria-label="Main navigation">
    <nav class="nav-group">
        <a href="{{ route('user.index') }}" @class(['nav-link-item', 'active' => request()->routeIs('user.index')]) @if (request()->routeIs('user.index')) aria-current="page" @endif>
            <i class="fa-solid fa-house" aria-hidden="true"></i><span class="nav-text">Classes</span>
        </a>
        <a href="{{ route('user.archive') }}" @class(['nav-link-item', 'active' => request()->routeIs('user.archive')]) @if (request()->routeIs('user.archive')) aria-current="page" @endif>
            <i class="fa-solid fa-box-archive" aria-hidden="true"></i><span class="nav-text">Archived</span>
        </a>
        <a href="{{ route('user.setting') }}" @class(['nav-link-item', 'active' => request()->routeIs('user.setting')]) @if (request()->routeIs('user.setting')) aria-current="page" @endif>
            <i class="fa-solid fa-gear" aria-hidden="true"></i><span class="nav-text">Settings</span>
        </a>
    </nav>

    <x-enrolled-classess />
</aside>
