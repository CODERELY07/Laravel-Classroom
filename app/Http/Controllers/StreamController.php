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

        $user = auth()->user();
        $isTeacher = $classess->teachers()->where('user_id', $user->id)->exists();
        $isStudent = $classess->students()->where('user_id', $user->id)->exists();

        if(!$isTeacher && !$isStudent){
            return redirect()->route('user.index');
        }
        $teachers = $classess->teachers()->get();
        $students = $classess->students()->get();
        $class = DB::table('classess')->where('id', $id)->first();
        if(empty($class)){
            return view('user.index');
        }
       
        $classworks = Classess::with([
            'questions' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'materials' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }
        ])->where('id', $id)->get();
        
        return view('user.stream', ['data' => $class, 'teachers' => $teachers , 'students' => $students, 'classworks' => $classworks ]);
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
    public function search(Request $request, string $id): JsonResponse
    {
        $data = $request->get('data');
    
        if (empty($data) || strlen($data) < 3) {
            return response()->json([
                'data' => []
            ]);
        }
    
        $users = DB::table('users')
        ->leftJoin('class_members', 'users.id', '=', 'class_members.user_id')
        ->where('users.email', 'like', '%' . $data . '%')
        ->select('users.*', DB::raw('COALESCE(class_members.class_id, "default_value") as class_id'))
        ->where(function ($query) use ($id) {
            $query->whereNull('class_members.class_id')
                  ->orWhere('class_members.class_id', '!=', $id);
        })
        ->get();

        return response()->json([
            'data' => $users
        ]);
    }
    public function store(Request $request)
    {
        $addedUserIds = $request->input('added_users', []);
        $class = Classess::find($request->input('id')); 

        foreach ($addedUserIds as $userId) {
            $user = User::find($userId);
           
            DB::table('class_members')->insert([
                'class_id' => $class->id,
                'user_id' => $user->id,
                'role' => $user->role,
                'joined_at' => now(),
            ]);
        }
        return response()->json( ['success' => 'Users added Successfully!','redirectUrl' => route('stream', [$class->id])]);
    }
}
