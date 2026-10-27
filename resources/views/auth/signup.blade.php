@extends('layouts.auth')
@section('title', 'Create account')

@section('content')
    <h1>Create your account</h1>
    <p class="auth-sub">It takes less than a minute.</p>

    <form action="{{ route('signup.post') }}" data-ajax-form novalidate>
        <div class="form-alert"></div>
        <x-field name="name" label="Full name" placeholder="Jamie Rivera" autocomplete="name" maxlength="100" required />
        <x-field name="email" label="Email" type="email" placeholder="you@school.edu" autocomplete="email" required />
        <x-password-field name="password" label="Password" autocomplete="new-password" hint="At least 8 characters." required />

        <fieldset class="mb-3">
            <legend class="form-label">I am a…</legend>
            <div class="role-picker" data-error-group="role">
                <input type="radio" name="role" id="role-teacher" value="teacher" required>
                <label for="role-teacher">
                    <i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i>
                    Teacher
                    <small>Create classes and grade work</small>
                </label>
                <input type="radio" name="role" id="role-student" value="student">
                <label for="role-student">
                    <i class="fa-solid fa-user-graduate" aria-hidden="true"></i>
                    Student
                    <small>Join classes with a code</small>
                </label>
            </div>
            <div class="invalid-feedback d-block" data-error-for="role"></div>
        </fieldset>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="terms" id="terms" value="1" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>
            </label>
            <div class="invalid-feedback d-block" data-error-for="terms"></div>
        </div>

        <button class="btn btn-primary btn-lg w-100" type="submit">Create account</button>
    </form>

    @include('auth.partials.terms')
@endsection

@section('footer')
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
@endsection
