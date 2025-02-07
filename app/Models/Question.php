<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['title','question', 'description', 'due_date', 'points', 'user_id', 'class_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function class()
    {
        return $this->belongsTo(Classess::class);
    }
    public function answers()
    {
        return $this->hasMany(ClassMemberQuestionAnswers::class, 'question_id', 'id');
    }


    
}

