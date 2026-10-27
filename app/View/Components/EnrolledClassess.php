<?php

namespace App\View\Components;

use App\Models\Classess;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/** Sidebar list of the signed-in user's active classes. */
class EnrolledClassess extends Component
{
    public Collection $classess;

    public function __construct()
    {
        $user = auth()->user();

        $this->classess = $user
            ? Classess::forMember($user)->where('status', Classess::STATUS_ACTIVE)->orderBy('classname')->get(['id', 'classname', 'section'])
            : collect();
    }

    public function render(): View|Closure|string
    {
        return view('components.enrolled-classess');
    }
}
