<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mail;

class ForgotPasswordController extends Controller
{
    public function showForgetPasswordForm(){
        return view('auth.forgetPassword');
    }

    public function submitForgetPassword(Request $request):JsonResponse{
        $request->validate([
            'email'=> 'required|email|exists:users',
        ]);
        
        $token = Str::random(64);

        $existingToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if($existingToken){
            $createdTime = Carbon::parse($existingToken->created_at);
            if($createdTime->diffInMinutes(now()) < 60){
                return response()->json(['success' => 'We already send the reset password link in your email. Please check again!']);
            }else{
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            }
        }
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);
        
        Mail::send('email.forgetPassword', ['token' => $token, 'email' => $request->email] , function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json(['success' => 'We have e-mailed your password reset link, Please check your email!']);
    }

    public function showResetPasswordForm(Request $request, $token){
        $email = $request->email;
        return view('auth.forgetPasswordLink', ['token' => $token, 'email' => $email]);
    }
    public function submitResetPasswordForm(Request $request):JsonResponse{
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
        ->where(['email' => $request->email , 'token' => $request->token ])->first();

        if(!$updatePassword){
            return response()->json(['error' => 'Invalid token!']);
        }

        $user = User::where('email', $request->email )->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email )->delete();

        return response()->json(['success' => 'Your password has been changed!','redirectUrl' => '/login']);
    }
}
