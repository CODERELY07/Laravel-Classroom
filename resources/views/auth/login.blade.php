@extends('layouts.auth')
@section('title', 'Sign in')

@section('content')
    <h1>Welcome back</h1>
    <p class="auth-sub">Sign in to get to your classes.</p>

    <form action="{{ route('login.post') }}" data-ajax-form novalidate>
        <div class="form-alert"></div>
        <x-field name="email" label="Email" type="email" placeholder="you@school.edu" autocomplete="email" required />

        <x-password-field name="password" label="Password" required />

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" value="1">
                <label class="form-check-label" for="remember_me">Keep me signed in</label>
            </div>
            <a href="{{ route('forget.password.get') }}" class="small">Forgot password?</a>
        </div>

        <button class="btn btn-primary btn-lg w-100" type="submit">Sign in</button>
    </form>
@endsection

@section('footer')
    New here? <a href="{{ route('signup') }}">Create an account</a>
@endsection
