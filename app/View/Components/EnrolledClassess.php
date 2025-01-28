<?php

namespace App\View\Components;

use Auth;
use Closure;
use DB;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class EnrolledClassess extends Component
{
    public $classess;
    public $classInitial;
    public function __construct()
    {
        $user = auth()->user()->id;
        $this->classess = DB::table('classess')->join('class_members', 'classess.id', '=' , 'class_members.class_id')->where('class_members.user_id', $user)->select('class_members.*', 'classess.*', DB::raw('classess.id as classID'))->get();

        if($this->classess->isNotEmpty()){
            $classname = $this->classess->first()->classname;
            $this->classInitial = substr($classname, 0, 1);
        }else{
            $this->classInitial = null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.enrolled-classess');
    }
}
