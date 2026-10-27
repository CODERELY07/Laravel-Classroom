<?php

namespace App\Http\Controllers;

use App\Models\ClassMember;
use App\Models\ClassMemberQuestionAnswers;
use App\Models\Classess;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GradeController extends Controller
{
    public function show(Classess $class, User $student)
    {
        Gate::authorize('teach', $class);

        $membership = $class->members()
                            ->where('user_id', $student->id)
                            ->where('role', ClassMember::ROLE_STUDENT)
                            ->firstOrFail();

        $questions = $class->questions()
            ->with(['answers' => fn ($q) => $q->where('class_member_id', $membership->id)])
            ->latest()
            ->get();

        return view('user.class.grade', [
            'class' => $class,
            'student' => $student,
            'questions' => $questions,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'answer_id' => 'required|integer|exists:class_member_question_answers,id',
            'grade' => 'nullable|integer|min:0',
        ]);

        $answer = ClassMemberQuestionAnswers::with('question.classroom')->findOrFail($request->answer_id);
        Gate::authorize('teach', $answer->question->classroom);

        $max = $answer->question->points;
        if ($request->filled('grade') && (int) $request->grade > $max) {
            return $this->error("The grade can't be higher than {$max}.");
        }

        $answer->grade = $request->filled('grade') ? (int) $request->grade : null;
        $answer->save();

        return response()->json(['updatedGrade' => $answer->grade, 'points' => $max]);
    }
}
