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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    if(auth()->check()){
        return redirect(route('user.index'));
    }
    return view('home.index');
})->name('home');

// Logged in users
Route::middleware('auth')->group(function(){
    // User Controller
    Route::controller(UserController::class)->group(function(){
        Route::get('/user', 'index')->name('user.index');
        Route::get('/user/archive', 'archive')->name('user.archive');
        Route::get('/user/setting', 'setting')->name('user.setting');
        Route::get('/user/setting/name', 'changeName')->name('user.changeName');
        Route::post('/user/setting/name/{id}', 'changeNamePost')->name('user.changeNamePost');
        Route::get('/user/setting/account', 'accountSetting')->name('user.accountSetting');
        Route::post('/user/setting/account/{id}', 'changePassword')->name('user.changePassword');
        Route::post('/user/profile', 'changeProfile')->name('user.changeProfile');
    });
   
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
        Route::post('/unenrollClass/{id}', 'unenrollClass')->name('unenrollClass.post');
    });

    //Stream Controller
    Route::controller(StreamController::class)->group(function(){
        Route::get('/stream/{id}', 'show')->name('stream');
        Route::post('/stream/{class_id}/unenroll', [StreamController::class, 'unenrollStudents'])->name('unenroll');
        Route::get('/stream/search/{id}', 'search')->name('stream.search');
        Route::post('/stream/save', 'store')->name('stream.store');
    });

    //Materials
    Route::post('/material/store', [MaterialController::class, 'store'])->name('material.store');
    Route::get('/material/{id}', [MaterialController::class, 'show'])->name('material.show');
    Route::get('storage/materials/{file}', [MaterialController::class, 'showMaterialFile']);
    //questions
    Route::resource('question', QuestionController::class);
    //Grade
    Route::post('/grade/update', [GradeController::class, 'update'])->name('grade.update');
    Route::get('/grade/{classID}/user/{userID}', [GradeController::class, 'show'])->name('grade.show'); 
     

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/class/{className}', function(){
        return view('user.class');
    })->name('user.class')->whereAlpha('className');
    Route::get('/account', function(){
        return view('user.accountManagement');
    })->name('account');

    Route::post('/question/turnin', ClassMemberQuestionAnswersController::class)->name('turnIn');

    // Chats
    Route::post('/fire/message', [ChatAppController::class, 'fireMessage'])->name('send.message');
    Route::get('/chat/{classID}', [ChatAppController::class, 'getChat'])->name('chats');
});

// not logged in
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