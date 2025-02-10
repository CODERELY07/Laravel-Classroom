<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassMember extends Model
{
    public function answers()
    {
        return $this->hasMany(ClassMemberQuestionAnswers::class, 'class_member_id', 'id');
    }
}

