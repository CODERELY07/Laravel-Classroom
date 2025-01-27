@extends('layouts.app')
@section('title', 'Settings')

@section('content')

    <div class="sub-container rounded shadow-1 p-2 m-4">
        <div class="msg alert" style="display: none">

        </div>
        <h2 class="text-start">Change Password</h2>
        <form id="changePassword" action="{{route('user.changePassword', $user->id)}}">
            @csrf
            <div class="mt-2">New Password:</label>
                <input type="password" name="newPass" id="newPass" class="form-control">
            </div>
            <div class="mt-2">Confirm Password:</label>
                <input type="password" name="newPass_confirmation" id="newPass_confirmation" class="form-control">
            </div>
            <div class="mt-2">
                <a href="{{ route('user.setting') }}" >Cancel</a>
                <input type="submit" value="Save">
            </div>
        </form>
        
    </div>
@endsection 