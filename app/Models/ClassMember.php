<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassMember extends Model
{
    // Values of the class_members.role enum
    public const ROLE_TEACHER = 'Teacher';
    public const ROLE_STUDENT = 'Student';

    public $timestamps = false;

    protected $fillable = ['class_id', 'user_id', 'role', 'joined_at'];

    protected function casts(): array
    {
        return ['joined_at' => 'datetime'];
    }

    /** Map a user's account role to the class membership role. */
    public static function roleFor(User $user): string
    {
        return $user->isTeacher() ? self::ROLE_TEACHER : self::ROLE_STUDENT;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classess::class, 'class_id');
    }

    public function answers()
    {
        return $this->hasMany(ClassMemberQuestionAnswers::class, 'class_member_id', 'id');
    }

    public function isTeacher(): bool
    {
        return strcasecmp($this->role, self::ROLE_TEACHER) === 0;
    }
}
