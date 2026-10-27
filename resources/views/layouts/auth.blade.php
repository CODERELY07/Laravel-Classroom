@extends('layouts.clean')

@section('body')
    <main id="main" class="auth-page">
        <a href="{{ route('home') }}" class="brand auth-brand">
            <span class="brand-mark" aria-hidden="true">ME</span>
            <span class="brand-name">Classroom</span>
        </a>

        <div class="auth-card">
            @yield('content')
        </div>

        @hasSection('footer')
            <p class="auth-foot">@yield('footer')</p>
        @endif
    </main>
@endsection
