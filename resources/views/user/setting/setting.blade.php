@extends('layouts.app')
@section('title', 'Settings')

@section('content')

    <div class="sub-container rounded shadow-1 p-2 m-4">
        <ul class="list-group">
            <a href="{{route('user.changeName')}}">
                <li class="list-group-item border-0  list-group-item-action">
                    <div>
                        <span>Name:</span>
                        <p>{{ $user->name }}</p>
                    </div>
                </li>
            </a>
            
            <a href="{{route('user.accountSetting')}}">
                <li class="list-group-item border-0 list-group-item-action">
                    <div>
                        <span>Account Security:</span>
                        <p>Change Password</p>
                    </div>
                </li>
            </a>
            
        </ul>
    </div>
@endsection 