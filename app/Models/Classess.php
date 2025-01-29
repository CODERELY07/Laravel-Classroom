<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classess extends Model
{
    protected $table = 'classess';
    protected $fillable = [
        'classname', 
        'section',
        'subject',
        'class_code'
    ];

public function teachers() {
    return $this->belongsToMany(User::class, 'class_members', 'class_id', 'user_id')
                ->withPivot('role')
                ->where('class_members.role', 'Teacher'); 
}
public function students() {
    return $this->belongsToMany(User::class, 'class_members', 'class_id', 'user_id')
                ->withPivot('role')
                ->where('class_members.role', 'Student'); 
}


}
