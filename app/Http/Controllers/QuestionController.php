<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
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
        return view('user.class.question',['questionsData' => $questionsData, 'questions' => $questions]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
