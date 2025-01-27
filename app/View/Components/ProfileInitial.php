<?php

namespace App\View\Components;

use Auth;
use Illuminate\View\Component;

class ProfileInitial extends Component
{
    public $profile;
    public function __construct(
       
    ) {
        if(Auth::check()){
            $this->profile = Auth::user()->profile;
        }
    }

    public function render()
    {
        return view('components.profile-initial');
    }
}
