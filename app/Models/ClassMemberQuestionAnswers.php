<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ClassMemberQuestionAnswers extends Model
{
    protected $fillable = ['class_member_id', 'question_id', 'answer'];

    public function classMember()
    {
        return $this->belongsTo(ClassMember::class, 'class_member_id', 'id');
    }

    // Define the inverse relationship to Question
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

}

