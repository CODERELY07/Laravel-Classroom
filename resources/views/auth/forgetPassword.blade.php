@extends('layouts.auth')
@section('title', 'Reset password')

@section('content')
    <h1>Forgot your password?</h1>
    <p class="auth-sub">Enter the email you signed up with and we’ll send you a link to choose a new one.</p>

    <form action="{{ route('forget.password.post') }}" data-ajax-form data-reset-on-success novalidate>
        <div class="form-alert"></div>
        <x-field name="email" label="Email" type="email" placeholder="you@school.edu" autocomplete="email" required />
        <button type="submit" class="btn btn-primary btn-lg w-100">Send reset link</button>
    </form>
@endsection

@section('footer')
    Remembered it? <a href="{{ route('login') }}">Back to sign in</a>
@endsection
