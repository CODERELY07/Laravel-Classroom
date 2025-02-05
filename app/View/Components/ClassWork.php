<?php

namespace App\View\Components;

use App\Models\Question;
use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ClassWork extends Component
{
    public $classID;
    public function __construct($classID)
    {
        $this->classID = $classID;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $informations = Question::where('class_id', $this->classID)->get();
        return view('components.class-work', ['informations' => $informations]);
    }
}
