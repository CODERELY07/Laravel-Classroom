@extends('layouts.auth')
@section('title', 'Choose a new password')

@section('content')
    <h1>Choose a new password</h1>
    <p class="auth-sub">Pick something you haven’t used here before.</p>

    <form action="{{ route('reset.password.post') }}" data-ajax-form novalidate>
        <div class="form-alert"></div>
        <input type="hidden" name="token" value="{{ $token }}">
        <x-field name="email" label="Email" type="email" :value="$email ?? old('email')" autocomplete="email" required />
        <x-password-field name="password" label="New password" autocomplete="new-password" hint="At least 8 characters." required />
        <x-password-field name="password_confirmation" label="Confirm new password" autocomplete="new-password" required />
        <button type="submit" class="btn btn-primary btn-lg w-100">Reset password</button>
    </form>
@endsection

@section('footer')
    <a href="{{ route('login') }}">Back to sign in</a>
@endsection
