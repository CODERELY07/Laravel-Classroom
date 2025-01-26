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
}
