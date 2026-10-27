<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassMemberQuestionAnswers extends Model
{
    protected $fillable = ['class_member_id', 'question_id', 'answer'];

    protected function casts(): array
    {
        return ['grade' => 'integer'];
    }

    public function classMember()
    {
        return $this->belongsTo(ClassMember::class, 'class_member_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function isGraded(): bool
    {
        return $this->grade !== null;
    }
}
