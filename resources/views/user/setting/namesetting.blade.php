@extends('layouts.app')
@section('title', 'Settings')

@section('content')

    <div class="sub-container rounded shadow-1 p-2 m-4">
        <div class="msg alert" style="display: none">

        </div>
        <form id="changeName" action="{{ route('user.changeNamePost', $user->id) }}">
            @csrf
            <div class="mt-2">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
            </div>
            <div class="mt-2">
                <a href="{{ route('user.setting') }}" >Cancel</a>
                <input type="submit" value="Save">
            </div>
        </form>
        
    </div>
@endsection 