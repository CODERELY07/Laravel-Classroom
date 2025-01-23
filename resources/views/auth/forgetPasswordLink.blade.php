
@extends('layouts.clean', [
    'cssBody' => 'signin-body'
])
@section('title', 'Reset Password')

@section('childContent')
    <div class=" mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Reset Password</div>
                    <div class="card-body">
                        <div class="msg alert" style="display: none">
                            {{-- Message --}}
                        </div>
                        <form id="forgetPasswordLink" action="{{ route('reset.password.post') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email ?? old('email')}}">
                            <div class="form-group row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                <div class="col-md-6">
                                    <input type="password" value="{{ old('password')}}" id="password" class="form-control" name="password" autofocus>
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>
                                <div class="col-md-6">
                                    <input type="password" value="{{ old('password_confirmation')}}" id="password-confirm" class="form-control" name="password_confirmation" autofocus>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

