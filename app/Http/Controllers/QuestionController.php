<?php

namespace App\Http\Controllers;

use App\Models\Classess;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'class_id' => 'required|integer|exists:classess,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'question' => 'required|array|min:1|max:50',
            'question.*' => 'required|string|max:1000',
            'points' => 'required|integer|min:1|max:1000',
            'duedate' => 'required|date|after_or_equal:today',
        ], [
            'question.*.required' => 'Fill in every question or lower the number of items.',
            'duedate.after_or_equal' => 'The due date cannot be in the past.',
        ], ['duedate' => 'due date', 'description' => 'instructions']);

        $class = Classess::findOrFail($data['class_id']);
        Gate::authorize('teach', $class);
        $this->ensureActive($class);

        $class->questions()->create([
            'title' => $data['title'],
            'question' => array_values($data['question']),
            'description' => $data['description'],
            'due_date' => $data['duedate'],
            'points' => $data['points'],
            'user_id' => $request->user()->id,
        ]);

        return $this->success('Question posted.', redirectUrl: route('stream', $class->id).'#classwork');
    }

    /** Deleting a question also deletes every answer and grade for it. */
    public function destroy(Question $question): JsonResponse
    {
        $class = $question->classroom;
        Gate::authorize('teach', $class);
        $this->ensureActive($class);

        $question->delete();

        return $this->success('Question deleted.', redirectUrl: route('stream', $class->id).'#classwork');
    }

    public function show(Request $request, Question $question)
    {
        $question->load('user', 'classroom');
        $class = $question->classroom;
        Gate::authorize('view', $class);

        $canTeach = Gate::allows('teach', $class);
        $membership = $class->membershipFor($request->user());

        $answer = $canTeach ? null : $question->answers()->where('class_member_id', $membership->id)->first();

        return view('user.class.question', [
            'question' => $question,
            'class' => $class,
            'canTeach' => $canTeach,
            'answer' => $answer,
            'submissionCount' => $canTeach ? $question->answers()->count() : null,
            'studentCount' => $canTeach ? $class->students()->count() : null,
        ]);
    }
}
