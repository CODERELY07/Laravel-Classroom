<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassMemberQuestionAnswers;
class ClassMemberQuestionAnswersController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'answer' => 'required|string'
        ]);
        
        ClassMemberQuestionAnswers::create([
            'answer' => $request->answer,
            'class_member_id' => $request->class_member_id,
            'question_id' => $request->question_id
        ]);
    }
}
