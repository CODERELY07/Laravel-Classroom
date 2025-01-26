<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classess;
use DB;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index(){
        $joinClassess = DB::table('class_members')
        ->where('user_id', auth()->user()->id)
        ->pluck('class_id');
        $classess = Classess::whereIn('id', $joinClassess)->where('status','ok')->get(); 
        return view('user.index', ['classess' => $classess]);
    }
    public function arhive(){
        $joinClassess = DB::table('class_members')
        ->where('user_id', auth()->user()->id)
        ->pluck('class_id');
        $classess = Classess::whereIn('id', $joinClassess)->where('status','archive')->get(); 
        return view('user.archiveclass', ['classess' => $classess]);
    }
}
