<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassMember;
use App\Models\ClassMemberQuestionAnswers;
use App\Models\Question;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function show(string $classID, string $userID)
    {
        $classMembers = ClassMember::where('user_id', $userID)->first();
        $questions = Question::where('class_id', $classID)
        ->with(['answers' => function($query) use ($classMembers) {
            $query->where('class_member_id', $classMembers->id);
        }])
        ->get();
        return view('user.class.grade', ['questions' => $questions]);
    }    
    public function update(Request $request)
    {
        $request->validate([
            'answer_id' => 'required|exists:class_member_question_answers,id',
            'grade' => 'nullable|numeric'
        ]);
       
        $answer = ClassMemberQuestionAnswers::findOrFail($request->answer_id);
        $answer->grade = $request->grade;
        $answer->save();

        return response()->json(['updatedGrade' => $answer->grade]);
    }
}
