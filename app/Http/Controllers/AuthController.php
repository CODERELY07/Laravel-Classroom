<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signin(){
        return view('auth.signin');
    }
    public function signup(){
        return view('auth.signup');
    }
    public function signinPost(Request $request):JsonResponse{
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials)){
            $redirectUrl = session()->pull('url.intended', route('user.index'));
            return response()->json(['success' => 'Signin Successfully', 'redirectUrl' => $redirectUrl]);
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
       
        return response()->json(['success' => 'User created successfully', 'redirectUrl' => route('signin')]);
    }
}
