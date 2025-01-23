@extends('layouts.clean', [
    'cssBody' => 'signin-body'
])
@section('title', 'Signin')

@section('childContent')
<div class="container">
    <a href="{{ route("home") }}" class="logo text-decoration-none text-dark text-center mb-3">
        <h1>
            ME Classroom
        </h1>
    </a>
    <div class="card p-4">
        <h6 class="">Reset Password</h6>
            <p>Enter Your account email address to send the verification code.</p>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="list-unstyled">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="card-body pt-0"> 
                <div class="msg alert" style="display: none">
                    {{-- Message --}}
                </div>
                <form id="forgetPassword" action="{{ route('forget.password.post') }}" method="post">
                    @csrf
                    <div class="mb-3">
                    <label for="email" class="form-label">Enter your email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="example@gmail.com" value="{{ old('email')}}" required>
                    </div>
                    <div class="mb-3">
                        <button type="submit" id="reset-btn" class="btn btn-primary w-100">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection