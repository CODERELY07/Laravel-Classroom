<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomChat extends Model
{
    protected $table = 'classroom_chats';
    protected $fillable = [
        'message', 
        'class_id',
        'user_id',
    ];

    public function class() {
        return $this->belongsTo(Classess::class, 'class_id', 'id', 'class_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id', 'user'); 
    }
}
