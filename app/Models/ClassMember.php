<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassMember extends Model
{
    // A class member can have many answers to questions
    // protected $primaryKey = 'class_member_id';
    public function answers()
    {
        return $this->hasMany(ClassMemberQuestionAnswers::class, 'class_member_id', 'id');
    }
}

