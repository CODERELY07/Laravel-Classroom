<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::get('/user', function(){
        return view('user.index');
    })->name('user.index');
});
Route::view('/', 'home.index')->name('home');
Route::get('/signin', [AuthController::class,'signin'])->name('signin');
Route::get('/signup', [AuthController::class,'signup'])->name('signup');
Route::post('/signup', [AuthController::class,'signupPost'])->name('signup.post');
Route::post('/signin', [AuthController::class,'signinPost'])->name('signin.post');
Route::fallback(function () {
    abort(404);
});