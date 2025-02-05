<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ClassMemberQuestionAnswers extends Model
{
    protected $fillable = ['class_member_id', 'question_id', 'answer'];

    public function classMember()
    {
        return $this->belongsTo(ClassMember::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

