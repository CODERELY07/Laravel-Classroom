<?php

namespace App\View\Components;

use App\Models\ClassMember;
use App\Models\Question;
use Illuminate\View\Component;

class ShowGrade extends Component
{
    public $questions;

    /**
     * Create a new component instance.
     */
    public function __construct($userID, $classID)
    {
        // Fetch the class member based on user ID
        $classMembers = ClassMember::where('user_id', $userID)->first();

        // Fetch questions with answers based on the class ID and class member
        $this->questions = Question::where('class_id', $classID)
            ->with(['answers' => function($query) use ($classMembers) {
                $query->where('class_member_id', $classMembers->id);
            }])
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.show-grade');
    }
}
