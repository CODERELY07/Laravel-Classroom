<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
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
    Route::get('/user/archive', [UserController::class, 'arhive'])->name('user.archive');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/material/{materialName}', function(){
        return view('user.material');
    })->name('user.material')->whereAlpha('materialName');
    Route::get('/class/{className}', function(){
        return view('user.class');
    })->name('user.class')->whereAlpha('className');
 
    Route::get('/account', function(){
        return view('user.accountManagement');
    })->name('account');
    
    // Route::get('/user/archive', function(){
    //     return view('user.archiveclass');
    // })->name('user.archive');

    // Class Controller
    Route::controller(ClassController::class)->group(callback: function(){
        Route::get('/classess', 'show')->name('classess.show');
        Route::get('/archive',  'showArchiveClass')->name('archive.show');
        Route::post('/createClass', 'create')->name('createClass.post');
        Route::post('/updateClass/{id}', 'update')->name('updateClass.post');
        Route::post('/archiveClass/{id}', 'archive')->name('archiveClass.post');
        Route::post('/unarchiveClass/{id}', 'unarchive')->name('unarchiveClass.post');
        Route::post('/destroyClass/{id}', 'destroy')->name('destroyClass.post');
        Route::post('/joinClass', 'join')->name('join.post');
    });
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
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get')->whereAlphaNumeric('token'); 
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post'); 
    });  
});

Route::fallback(function () {
    abort(404);
});