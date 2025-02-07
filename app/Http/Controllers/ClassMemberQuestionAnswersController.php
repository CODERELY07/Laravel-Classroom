<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassMemberQuestionAnswers;
use Illuminate\Support\Facades\DB;

class ClassMemberQuestionAnswersController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'answer' => 'required|string',
            'class_member_id' => 'required|integer',  
            'question_id' => 'required|integer',     
        ]);

        ClassMemberQuestionAnswers::updateOrCreate(
            [
                'class_member_id' => $request->class_member_id, 
                'question_id' => $request->question_id       
            ],
            [
                'answer' => $request->answer, 
            ]
        );
        return redirect()->route('question.show', $request->question_id);
    }
}
