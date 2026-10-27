<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * Uses Laravel's password broker, which hashes tokens, expires them after
 * auth.passwords.users.expire minutes and throttles repeat requests.
 */
class ForgotPasswordController extends Controller
{
    public function showForgetPasswordForm()
    {
        return view('auth.forgetPassword');
    }

    public function submitForgetPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(['email' => Str::lower($request->email)]);

        if ($status === Password::RESET_THROTTLED) {
            return $this->error('A reset link was sent recently. Please check your inbox or try again in a minute.', 429);
        }

        // Same answer whether or not the address exists, so accounts can't be enumerated.
        return $this->success('If an account exists for that address, a reset link is on its way.');
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.forgetPasswordLink', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function submitResetPasswordForm(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return $this->error('This reset link is invalid or has expired. Please request a new one.');
        }

        return $this->success('Your password has been changed. You can sign in now.', redirectUrl: route('login'));
    }
}
