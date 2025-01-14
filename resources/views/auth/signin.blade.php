@extends('layout.clean', [
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
    <div class="box-style">
        <div id="responseMessage" class="alert text-center text-dark" role="alert" style="display: none;"></div>
            <h2>Sign In Now</h2>
            <p>Enter your credentials to access your account</p>
            <form id="signinForm" class="form">
                
                <div class="mb-3">
                    <label class="text-start form-label" for="email">Email address</label>
                    <input class="form-control" name="email" id="email" type="email" placeholder="example@gmail.com"/>
                </div>
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <label class="form-label" style="margin-bottom:-20px" for="password">Password</label>
                    <a class="text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">Forgot password?</a>

                </div>
                <div class="mb-3">
                    <input class="form-control" name="password" id="password" placeholder="Your Password" type="password"/>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" name="remember_me" id="remember_me" type="checkbox" />
                    <label class="form-check-label" for="remember_me">
                        Remember Me
                    </label>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary" type="submit">Sign In</button>
                </div>
            </form>
            <p class="text-center text-signin mt-3">
                Don't have an account yet? <a href="#">Sign up</a>
            </p>

        </div>
        <!-- Reset Password Modal -->
        <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Reset Your Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="emailStage">
                <p>Enter Your account email address to send the verification code.</p>
                <form id="emailForm">
                    <div class="mb-3">
                    <label for="resetEmail" class="form-label">Enter your email address</label>
                    <input type="email" class="form-control" id="resetEmail" name="resetEmail" placeholder="example@gmail.com" required>
                    </div>
                    <div class="mb-3">
                    <button type="submit" id="reset-btn" class="btn btn-primary w-100">Submit</button>
                    </div>
                </form>
                </div>

                <!-- Stage 2: Verification Code -->
                <div id="codeStage" style="display: none;">
                <form id="codeForm">
                <p>Check Your email for your 6- digit code.</p>
                    <div class="mb-3">
                    <label for="resetCode" class="form-label">Enter the 6-digit code</label>
                    <input type="text" class="form-control" id="resetCode" name="resetCode" placeholder="6-digit code" maxlength="6" required>
                    </div>
                    <div class="mb-3">
                    <button type="submit" id="verify-button" class="btn btn-primary w-100">Verify Code</button>
                    </div>
                </form>
                <div id="codeResponseMessage" class="alert alert-info text-center" style="display:none;"></div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection