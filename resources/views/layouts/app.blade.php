@extends('layouts.clean')

@section('body')
    @include('layouts.partials.header')

    <div class="shell">
        @include('layouts.partials.sidebar')
        <div class="sidebar-backdrop" data-sidebar-close></div>

        <main id="main" class="main" tabindex="-1">
            @yield('content')
        </main>
    </div>

    @include('layouts.partials.profileModal')
    @include('layouts.partials.confirmModal')
@endsection
