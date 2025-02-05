<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassMember extends Model
{
    // A class member can have many answers to questions
    public function answers()
    {
        return $this->hasMany(ClassMemberQuestionAnswers::class);
    }
}

