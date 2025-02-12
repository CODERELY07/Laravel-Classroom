<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classess;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Str;

class ClassController extends Controller
{

     public function create(Request $request): JsonResponse
     {
         if (auth()->user()->role !== 'teacher') {
             return response()->json(['error' => 'You do not have permission to create a class.'], 403);
         }
     
         $request->validate([
             'classname' => 'required|string',
             'section' => 'required|string',
             'subject' => 'required|string',
         ]);
     
         $classCode = Str::random(8);
     
         $classId = DB::table('classess')->insertGetId([
             'classname' => $request->classname,
             'section' => $request->section,
             'subject' => $request->subject,
             'class_code' => $classCode,
             'status' => 'ok',
             'teacher_id' => auth()->user()->id,
             'created_at' => now(),
             'updated_at' => now(),
         ]);
     

         DB::table('class_members')->insert([
             'class_id' => $classId,
             'user_id' => auth()->user()->id,
             'role' => 'teacher',
             'joined_at' => now(),
         ]);
     
         return response()->json([
             'success' => "Class  $request->classnamecreated successfully.",
         ]);
     }
     
     public function join(Request $request):JsonResponse
     {
         
         $request->validate([
             'classcode' => 'required|string',
         ]);
     
         $class_code = $request->classcode;
         
         $class = Classess::where('class_code', $class_code)->first();   
         if (!$class) {
             return response()->json(['error' => 'Class not found']);
         }
     

         $alreadyJoined = DB::table('class_members')
             ->where('class_id', $class->id)
             ->where('user_id', auth()->user()->id)
             ->exists();
     
         if ($alreadyJoined) {
            return response()->json(['error' => 'You are already a member of this class.']);
        }
     
         DB::table('class_members')->insert([
             'class_id' => $class->id,
             'user_id' => auth()->user()->id,
             'role' => auth()->user()->role,
             'joined_at' => now(),
         ]);
     

         return response()->json(['success' => 'Successfully joined the class!', 'redirectUrl' => route('user.index')]);
     }

     public function show(): JsonResponse
     {
         $joinClassess = DB::table('class_members')
                           ->where('user_id', auth()->user()->id)
                           ->pluck('class_id');
         
         $classess = DB::table('classess')
                       ->join('users', 'classess.teacher_id', '=', 'users.id') 
                       ->whereIn('classess.id', $joinClassess)  
                       ->where('classess.status', 'ok') 
                       ->select('classess.*', 'users.*',  DB::raw('SUBSTRING(users.name, 1, 1) as first_letter'), DB::raw('users.id as user_id'), DB::raw('classess.id as class_id'))  
                       ->get();

         return response()->json([
             'html' => view('layouts.partials.classess', ['classess' => $classess])->render(),
             'classess' => $classess
         ]);
     }

    public function update(Request $request, string $id): JsonResponse
    {
        $teacher_id = auth()->user()->id;
        $isExist = DB::table('classess')
        ->where('id', $id)
        ->where('teacher_id', $teacher_id)
        ->exists();

        if(!$isExist){
            return response()->json(['error' => 'You dont have the permission to update this class!']);
        }
    
        $class = Classess::where('id', $id)
                        ->where('teacher_id', auth()->user()->id)
                        ->first();
    
        if (!$class) {
            return response()->json(['error' => 'Class not found or you do not have permission to update this class.'], 404);
        }
    
        $request->validate([
            'classname' => 'required|string',
            'section' => 'required|string',
            'subject' => 'required|string',
        ]);
    

        $class->update($request->all());
    
        return response()->json(['success' => 'Class updated successfully.']);
    }

    public function archive(string $id):JsonResponse{
        $teacher_id = auth()->user()->id;
        $isExist = DB::table('classess')
        ->where('id', $id)
        ->where('teacher_id', $teacher_id)
        ->exists();

        if(!$isExist){
            return response()->json(['error' => 'You dont have the permission to archive this class!']);
        }
        DB::table('classess')
        ->where('id', $id)
        ->where('teacher_id', $teacher_id)
        ->update(['status' => 'archive']);
        return response()->json(['success' => 'Classess Archive Successfully']);
    }
    public function unarchive(string $id):JsonResponse{
        $teacher_id = auth()->user()->id;
        $isExist = DB::table('classess')
        ->where('id', $id)
        ->where('teacher_id', $teacher_id)
        ->exists();

        if(!$isExist){
            return response()->json(['error' => 'You dont have the permission to unarchive this class!']);
        }
        DB::table('classess')
        ->where('id', $id)
        ->where('teacher_id', $teacher_id)
        ->update(['status' => 'ok']);
        return response()->json(['success' => 'Classess unarchive Successfully']);
    }

    public function unenrollClass(string $id):JsonResponse{
        $student_id = auth()->user()->id;
        $isExistClass = DB::table('classess')->where('id', $id)->exists();
        if(!$isExistClass){
            return response()->json(['error' => 'You are not enrolled in this class']);
        }
        $isEnrolled = DB::table('class_members')->where('class_id', $id)->where('user_id', $student_id)->exists();
        if(!$isEnrolled){
            return response()->json(['error' => 'You are not enrolled in this class!']);
        }
        DB::table('class_members')->where('class_id', $id)->where('user_id', $student_id)->delete();
        return response()->json(['success' => 'You are successfully unernolled in this class!']);
    }

    public function showArchiveClass():JsonResponse{
        $joinClassess = DB::table('class_members')
        ->where('user_id', auth()->user()->id)
        ->pluck('class_id');
    
        $classess = DB::table('classess')
        ->join('users', 'classess.teacher_id', '=', 'users.id') 
        ->whereIn('classess.id', $joinClassess)  
        ->where('classess.status', 'archive') 
        ->select('classess.*', 'users.*',  DB::raw('SUBSTRING(users.name, 1, 1) as first_letter'), DB::raw('users.id as user_id'), DB::raw('classess.id as class_id'))  
        ->get();

        return response()->json([
        'html' => view('layouts.partials.classess', ['classess' => $classess])->render(),
        'classess' => $classess
        ]);
    }
    public function destroy(string $id):JsonResponse
    {
        $teacher_id = auth()->user()->id;
        $isExist = DB::table('classess')
        ->where('id', $id)
        ->where('teacher_id', $teacher_id)
        ->exists();

        if(!$isExist){
            return response()->json(['error' => 'You dont have the permission to delete this class!']);
        }
        DB::table('classess')->where('id', $id)->delete();
        return response()->json(['success' => 'Classess deleted Successfully']);
    }
}
