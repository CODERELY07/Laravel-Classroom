<?php

namespace App\View\Components;

use Auth;
use DB;
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
    public function nameInitial(){  
        if(Auth::check()){
            $user_id = auth()->user()->id;
            $user_name = DB::table('users')->where('id', $user_id)->first()->name;
            $first_letter = substr($user_name, 0,1);
            return $first_letter;
        }
    }
    public function render()
    {
        return view('components.profile-initial');
    }
}
