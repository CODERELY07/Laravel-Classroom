<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatAppController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassMemberQuestionAnswersController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('user.index') : view('home.index');
})->name('home');

// Logged in users. auth.session ends other sessions when a password changes.
Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Account
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'index')->name('user.index');
        Route::get('/user/archive', 'archive')->name('user.archive');
        Route::get('/user/setting', 'setting')->name('user.setting');
        Route::post('/user/setting/name', 'changeNamePost')->name('user.changeNamePost');
        Route::post('/user/setting/password', 'changePassword')->name('user.changePassword');
        Route::post('/user/profile', 'changeProfile')->name('user.changeProfile');
    });

    // Classes
    Route::controller(ClassController::class)->group(function () {
        Route::get('/classess', 'show')->name('classess.show');
        Route::get('/archive', 'showArchiveClass')->name('archive.show');
        Route::post('/createClass', 'create')->name('createClass.post');
        Route::post('/joinClass', 'join')->middleware('throttle:10,1')->name('join.post');
        Route::post('/updateClass/{class}', 'update')->name('updateClass.post');
        Route::post('/archiveClass/{class}', 'archive')->name('archiveClass.post');
        Route::post('/unarchiveClass/{class}', 'unarchive')->name('unarchiveClass.post');
        Route::post('/destroyClass/{class}', 'destroy')->name('destroyClass.post');
        Route::post('/resetClassCode/{class}', 'resetCode')->name('resetCode.post');
        Route::post('/unenrollClass/{class}', 'unenrollClass')->name('unenrollClass.post');
    });

    // Class stream and people
    Route::controller(StreamController::class)->group(function () {
        Route::get('/stream/{class}', 'show')->name('stream');
        Route::post('/stream/{class}/unenroll', 'unenrollStudents')->name('unenroll');
        Route::get('/stream/{class}/search', 'search')->middleware('throttle:30,1')->name('stream.search');
        Route::post('/stream/{class}/teachers/{teacher}/remove', 'removeTeacher')->name('stream.removeTeacher');
        Route::post('/stream/{class}/members', 'store')->name('stream.store');
    });

    // Materials
    Route::post('/material/store', [MaterialController::class, 'store'])->name('material.store');
    Route::get('/material/{material}', [MaterialController::class, 'show'])->name('material.show');
    Route::get('/material/{material}/file', [MaterialController::class, 'download'])->name('material.file');
    Route::post('/material/{material}/delete', [MaterialController::class, 'destroy'])->name('material.destroy');

    // Questions and answers
    Route::resource('question', QuestionController::class)->only(['store', 'show']);
    Route::post('/question/{question}/delete', [QuestionController::class, 'destroy'])->name('question.destroy');
    Route::post('/question/turnin', ClassMemberQuestionAnswersController::class)->name('turnIn');

    // Grades
    Route::post('/grade/update', [GradeController::class, 'update'])->name('grade.update');
    Route::get('/grade/{class}/user/{student}', [GradeController::class, 'show'])->name('grade.show');

    // Chat
    Route::post('/fire/message', [ChatAppController::class, 'fireMessage'])->middleware('throttle:30,1')->name('send.message');
    Route::get('/chat/{class}', [ChatAppController::class, 'getChat'])->name('chats');
});

// Guests only
Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::get('/signup', 'signup')->name('signup');
        Route::post('/signup', 'signupPost')->middleware('throttle:10,1')->name('signup.post');
        Route::post('/login', 'loginPost')->name('login.post');
    });

    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('/forget-password', 'showForgetPasswordForm')->name('forget.password.get');
        Route::post('/forget-password', 'submitForgetPassword')->middleware('throttle:6,1')->name('forget.password.post');
        Route::get('/reset-password/{token}', 'showResetPasswordForm')->name('password.reset');
        Route::post('/reset-password', 'submitResetPasswordForm')->middleware('throttle:6,1')->name('reset.password.post');
    });
});
