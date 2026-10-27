<header class="topbar">
    <button type="button" class="icon-btn sidebar-toggle" data-sidebar-toggle aria-controls="sidebar" aria-expanded="false" aria-label="Open navigation">
        <i class="fa-solid fa-bars" aria-hidden="true"></i>
    </button>

    <a href="{{ route('user.index') }}" class="brand">
        <span class="brand-mark" aria-hidden="true">ME</span>
        <span class="brand-name">Classroom</span>
    </a>

    @hasSection('crumb')
        <nav class="crumbs min-w-0" aria-label="Breadcrumb">
            <span aria-hidden="true">/</span>
            <span class="crumb-current">@yield('crumb')</span>
        </nav>
    @endif

    <div class="topbar-actions">
        @stack('topbar-actions')

        <div class="dropdown">
            <button type="button" class="icon-btn avatar-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu">
                <x-avatar :user="$authUser" size="sm" />
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <div class="account-head">
                    <x-avatar :user="$authUser" size="lg" />
                    <div class="min-w-0">
                        <strong class="text-truncate">{{ $authUser->name }}</strong>
                        <small class="d-block text-truncate">{{ $authUser->email }}</small>
                        <span class="pill pill-accent mt-1 text-capitalize">{{ $authUser->role }}</span>
                    </div>
                </div>
                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">
                    <i class="fa-regular fa-image" aria-hidden="true"></i> Change photo
                </button>
                <a class="dropdown-item" href="{{ route('user.setting') }}">
                    <i class="fa-solid fa-gear" aria-hidden="true"></i> Settings
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
