@extends('layouts.app')
@section('title', 'Settings')
@section('crumb', 'Settings')

@section('content')
<div class="page-narrow">
    <header class="page-header">
        <div>
            <h1 class="page-title">Settings</h1>
            <p class="page-subtitle">Manage how you appear to others and keep your account secure.</p>
        </div>
    </header>

    <div class="settings-grid">
        <section class="panel" aria-labelledby="profile-heading">
            <div class="panel-header"><h2 class="panel-title" id="profile-heading">Profile</h2></div>
            <div class="panel-body d-flex flex-wrap align-items-center gap-3">
                <x-avatar :user="$user" size="lg" />
                <div class="flex-fill min-w-0">
                    <div class="fw-medium">{{ $user->name }}</div>
                    <div class="small text-muted-2 text-truncate">{{ $user->email }} · <span class="text-capitalize">{{ $user->role }}</span></div>
                </div>
                <button type="button" class="btn btn-quiet" data-bs-toggle="modal" data-bs-target="#profileModal">Change photo</button>
            </div>
        </section>

        <section class="panel" aria-labelledby="name-heading">
            <div class="panel-header"><h2 class="panel-title" id="name-heading">Display name</h2></div>
            <form class="panel-body" action="{{ route('user.changeNamePost') }}" data-ajax-form novalidate>
                <div class="form-alert"></div>
                <x-field name="name" label="Name" :value="$user->name" maxlength="100" autocomplete="name" required
                         hint="Shown to teachers and classmates in every class you belong to." />
                <button type="submit" class="btn btn-primary">Save name</button>
            </form>
        </section>

        <section class="panel" aria-labelledby="password-heading">
            <div class="panel-header"><h2 class="panel-title" id="password-heading">Password</h2></div>
            <form class="panel-body" action="{{ route('user.changePassword') }}" data-ajax-form data-reset-on-success novalidate>
                <div class="form-alert"></div>
                <x-password-field name="current_password" label="Current password" required />
                <div class="row g-3">
                    <div class="col-sm-6"><x-password-field name="newPass" label="New password" autocomplete="new-password" hint="At least 8 characters." required /></div>
                    <div class="col-sm-6"><x-password-field name="newPass_confirmation" label="Confirm new password" autocomplete="new-password" required /></div>
                </div>
                <button type="submit" class="btn btn-primary">Change password</button>
            </form>
        </section>
    </div>
</div>
@endsection
