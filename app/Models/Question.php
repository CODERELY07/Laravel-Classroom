<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['title', 'question', 'description', 'due_date', 'points', 'user_id', 'class_id'];

    protected function casts(): array
    {
        return [
            'question' => 'array',
            'due_date' => 'date',
            'points' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classess::class, 'class_id');
    }

    public function answers()
    {
        return $this->hasMany(ClassMemberQuestionAnswers::class, 'question_id', 'id');
    }

    /** The list of prompts, tolerant of rows that were double-encoded. */
    public function items(): array
    {
        $items = $this->question;

        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        return array_values(array_filter((array) $items, fn ($item) => is_string($item) && trim($item) !== ''));
    }

    public function isPastDue(): bool
    {
        return $this->due_date && $this->due_date->endOfDay()->isPast();
    }
}
