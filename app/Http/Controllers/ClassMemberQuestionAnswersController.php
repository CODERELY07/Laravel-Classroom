<?php

namespace App\Http\Controllers;

use App\Models\ClassMemberQuestionAnswers;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClassMemberQuestionAnswersController extends Controller
{
    /**
     * Turn in (or update) the signed-in student's answer to a question.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'answer' => 'required|string|max:20000',
        ]);

        $question = Question::with('classroom')->findOrFail($data['question_id']);
        $membership = $question->classroom->membershipFor($request->user());

        abort_unless($membership && ! $membership->isTeacher(), 403, 'Only students of this class can turn in work.');

        if ($question->classroom->isArchived()) {
            return back()->with('error', 'This class is archived, so work can no longer be turned in.');
        }

        $existing = ClassMemberQuestionAnswers::where('class_member_id', $membership->id)
                                              ->where('question_id', $question->id)
                                              ->first();

        if ($existing?->isGraded()) {
            return back()->with('error', 'This work has already been graded, so it can no longer be changed.');
        }

        // Late work is accepted (and marked late), but on-time work can't be rewritten after the deadline.
        if ($existing && $question->isPastDue()) {
            return back()->with('error', 'The due date has passed, so your answer can no longer be changed.');
        }

        ClassMemberQuestionAnswers::updateOrCreate(
            ['class_member_id' => $membership->id, 'question_id' => $question->id],
            ['answer' => $data['answer']]
        );

        return redirect()->route('question.show', $question->id)
                         ->with('success', match (true) {
                             (bool) $existing => 'Your answer was updated.',
                             $question->isPastDue() => 'Turned in late. Your teacher will see it was submitted after the due date.',
                             default => 'Turned in. Nice work!',
                         });
    }
}
