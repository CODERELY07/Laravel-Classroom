<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassMember;
use App\Models\ClassMemberQuestionAnswers;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request):JsonResponse
    {
        $request->validate([
            'description' => 'required|string',
            'title' => 'required|string',
            'question' => 'required|array',
            'points' => 'required|integer',
            'duedate' => 'required|date'
        ]);

        Question::create([
            'title' => $request->title,
            'question' => json_encode($request->question),
            'description' => $request->description,
            'due_date' => $request->duedate,
            'points' => $request->points,
            'user_id' => auth()->user()->id,
            'class_id' => $request->class_id
        ]);
        return response()->json(['redirectUrl' => route('stream', [$request->class_id]) , 'success' => 'Question Created Successfully']); 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    $questionsData = Question::with('user')->where('id', $id)->first();
    
    $questions = json_decode($questionsData->question);

    $classMember = ClassMember::where('user_id', auth()->user()->id)
                                        ->where('class_id', $questionsData->class_id)
                                        ->first();
    $answer = ClassMemberQuestionAnswers::where('class_member_id', $classMember->id)
    ->where('question_id', $questionsData->id)
    ->first();

    return view('user.class.question', [
        'questionsData' => $questionsData,
        'questions' => $questions,
        'classMember' => $classMember ,
        'answer' => $answer ? $answer->answer : null,
    ]);
}
}
