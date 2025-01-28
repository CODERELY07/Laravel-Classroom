<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    public function show(string $id){
        $data = DB::table('classess')->where('id', $id)->first();
        return view('user.stream', ['data' => $data]);
    }
}
