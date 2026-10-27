<?php

namespace App\Policies;

use App\Models\ClassMember;
use App\Models\Classess;
use App\Models\User;

/**
 * Three levels of access to a class:
 *  - view:   any member (teacher or student) can read the class and chat.
 *  - teach:  teacher members can post work, grade and manage people.
 *  - manage: only the owner can edit, archive, restore or delete the class.
 */
class ClassessPolicy
{
    public function view(User $user, Classess $class): bool
    {
        return $class->members()->where('user_id', $user->id)->exists();
    }

    public function teach(User $user, Classess $class): bool
    {
        if ((int) $class->teacher_id === (int) $user->id) {
            return true;
        }

        return $class->members()
                     ->where('user_id', $user->id)
                     ->where('role', ClassMember::ROLE_TEACHER)
                     ->exists();
    }

    public function manage(User $user, Classess $class): bool
    {
        return (int) $class->teacher_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isTeacher();
    }
}
