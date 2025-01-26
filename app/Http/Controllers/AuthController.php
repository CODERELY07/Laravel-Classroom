<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Hash;
use Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(){
        if(Auth::check()){
            return redirect(route('user.index'));
        }
        return view('auth.login');
    }
    public function signup(){
        return view('auth.signup');
    }
    public function loginPost(Request $request):JsonResponse{
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        $remeber_me = $request->has('remember_me');

        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials, $remeber_me)){
            $redirectUrl = session()->pull('url.intended', route('user.index'));
            return response()->json(['success' => 'Login Successfully', 'redirectUrl' => $redirectUrl]);
        }
        return response()->json(['error' => 'Invalid Credentials']);
    }
    public function signupPost(Request $request):JsonResponse{
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|string|in:Teacher,Student',
            'terms' => 'required',
        ]);
        
        User::create([
            'name'=> $request->name,
            'email' => $request->email,
            'role' =>$request->role,
            'password' => Hash::make($request->password)
        ]);
       
        return response()->json(['success' => 'User created successfully', 'redirectUrl' => route('login')]);
    }

    public function logout():RedirectResponse{
        Session::flush();
        Auth::logout();

        return redirect(route('login'));
    }
}
