<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classess;
use App\Models\User;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    public function show(string $id){
        $classess = Classess::findOrFail($id);
        $teachers = $classess->teachers()->get();
        $students = $classess->students()->get();
        $class = DB::table('classess')->where('id', $id)->first();
        return view('user.stream', ['data' => $class, 'teachers' => $teachers , 'students' => $students]);
    }
    public function unenrollStudents(Request $request, $class_id):JsonResponse
    {
        $class = Classess::findOrFail($class_id);
    
        if ($request->has('unenroll')) {
            $class->students()->detach($request->unenroll);
            return response()->json(['success' => 'Selected students have been removed Successfully!', 'redirectUrl' => route('stream', [$class_id])]);
        }
        return response()->json(['error' => 'No students selected!', 'redirectUrl' => route('stream', [$class_id])]);
    }

}
