<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    public function login()
    {
        return view('auth.login');
    }

    public function signup()
    {
        return view('auth.signup');
    }

    public function loginPost(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Too many sign-in attempts. Try again in {$seconds} seconds.",
            ])->status(429);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember_me'))) {
            RateLimiter::hit($throttleKey);

            return $this->error('The email or password you entered is incorrect.', 422);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return $this->success('Welcome back!', redirectUrl: redirect()->intended(route('user.index'))->getTargetUrl());
    }

    public function signupPost(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'string', Password::min(8)],
            'role' => 'required|string|in:teacher,student,Teacher,Student',
            'terms' => 'accepted',
        ]);

        $user = User::create([
            'name' => trim($data['name']),
            'email' => Str::lower($data['email']),
            'role' => Str::lower($data['role']),
            'password' => $data['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->success('Account created. Welcome aboard!', redirectUrl: route('user.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
