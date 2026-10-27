<?php

use App\Models\Classess;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Class chat: only members of the class may listen.
Broadcast::channel('classroom.{classId}', function ($user, $classId) {
    $class = Classess::find($classId);

    return $class && $user->can('view', $class);
});
