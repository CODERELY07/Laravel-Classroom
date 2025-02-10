<?php

namespace App\View\Components;

use App\Models\Material;
use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Materials extends Component
{
    /**
     * Create a new component instance.
     */
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
        $materials = Material::where('class_id', $this->classID)->get();
        return view('components.materials', ['materials' => $materials]);
    }
}
