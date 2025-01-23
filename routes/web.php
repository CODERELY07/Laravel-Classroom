<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    if(auth()->check()){
        return redirect(route('user.index'));
    }
    return view('home.index');
})->name('home');

Route::middleware('auth')->group(function(){
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/material/{materialName}', function(){
        return view('user.material');
    })->name('user.material');
    Route::get('/class/{className}', function(){
        return view('user.class');
    })->name('user.class');
 
    Route::get('/account', function(){
        return view('user.accountManagement');
    })->name('account');
});

Route::middleware('guest')->group(function(){
    Route::controller(AuthController::class)->group(function(){
        Route::get('/login', 'login')->name('login');
        Route::get('/signup', 'signup')->name('signup');
        Route::post('/signup', 'signupPost')->name('signup.post');
        Route::post('/login', 'loginPost')->name('login.post');
    });

    // Forget Password Routes
    Route::controller(ForgotPasswordController::class)->group(function(){
        Route::get('/forget-password', 'showForgetPasswordForm')->name('forget.password.get');
        Route::post('/forget-password', 'submitForgetPassword')->name('forget.password.post');
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get'); 
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post'); 
    });  
});

Route::fallback(function () {
    abort(404);
});