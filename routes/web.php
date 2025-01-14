<?php

use App\Http\Controllers\SigninController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home.index')->name('home');
Route::get('/signin', [SigninController::class,'index'])->name('signin');
