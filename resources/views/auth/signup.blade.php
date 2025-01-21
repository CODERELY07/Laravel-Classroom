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
        <div class="box-style">
            <div id="responseMessage" class="alert text-dark text-center" role="alert" style="display: none;"></div>
            <h2>Sign Up</h2>
            <p>Enter your details to create your account</p>
            <div class="msg alert" style="display: none">

            </div>
            <form action="{{ route('signup.post') }}" class="form signup-form" id="signupForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Full Name</label>
                    <input class="form-control" name="name" id="name" type="text" placeholder="Full Name"/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-control" name="email" id="email" type="email" placeholder="example@gmail.com"/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" name="password" id="password" type="password" placeholder="Your Password"/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="role">Role</label>
                    <select class="form-control" id="role" name="role">
                        <option value="">Select Role</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Student">Student</option>
                    </select>
                </div>
                <!-- Terms and Conditions Checkbox -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required/>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>.
                    </label>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary" type="submit">Sign Up</button>
                </div>
            </form>
            <p class="text-center text-auth-footer mt-3">
                Already have an account? <a href="{{ route('login')}}">Sign In</a>
            </p>
        </div>
    </div>

    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                <h6>1. Agreement to Terms</h6>
                <p>By accessing or using this website, you agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree with these terms, you must not access or use the website.</p>

                <h6>2. Privacy Policy</h6>
                <p>We respect your privacy and are committed to protecting your personal data. Our Privacy Policy outlines how we collect, use, and safeguard your personal information.</p>

                <h6>3. User Responsibilities</h6>
                <p>You agree to use this website only for lawful purposes and in a way that does not infringe the rights of, restrict, or inhibit the use of this website by any third party. You must not attempt to disrupt the functionality of the website or engage in any fraudulent activity.</p>

                <h6>4. Account Security</h6>
                <p>If you create an account on our website, you are responsible for maintaining the confidentiality of your account information, including your password. You agree to notify us immediately if you suspect any unauthorized use of your account.</p>

                <h6>5. Content Ownership</h6>
                <p>All content on this website, including text, graphics, images, and software, is the property of the website owner or its licensors and is protected by copyright laws. You may not use, reproduce, or distribute any content from this website without permission.</p>

                <h6>6. Prohibited Activities</h6>
                <p>You agree not to engage in any of the following activities on our website:</p>
                <ul>
                    <li>Distribute malware or other harmful software</li>
                    <li>Harass, intimidate, or discriminate against others</li>
                    <li>Violate any applicable laws or regulations</li>
                    <li>Attempt to gain unauthorized access to the website’s systems or data</li>
                </ul>

                <h6>7. Termination of Access</h6>
                <p>We reserve the right to suspend or terminate your access to the website at any time, without notice, for any reason, including but not limited to violation of these Terms and Conditions.</p>

                <h6>8. Limitation of Liability</h6>
                <p>We are not liable for any direct, indirect, incidental, or consequential damages resulting from your use of the website, including loss of data or loss of profits. You use the website at your own risk.</p>

                <h6>9. Changes to Terms</h6>
                <p>We may update these Terms and Conditions from time to time. When we do, we will post the updated terms on this page and update the "Last Updated" date at the top. Your continued use of the website after any changes to the terms constitutes acceptance of those changes.</p>

                <h6>10. Governing Law</h6>
                <p>These Terms and Conditions are governed by the laws of [Your Country/State], and any disputes related to these terms will be subject to the exclusive jurisdiction of the courts in [Your Jurisdiction].</p>

                <h6>11. Contact Information</h6>
                <p>If you have any questions about these Terms and Conditions, please contact us at sample@gmail.com.</p>
            </div>
        </div>
    </div>
</div>
</div>
@endsection