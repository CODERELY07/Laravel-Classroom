<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'description', 
        'file', 
        'user_id', 
        'class_id'
    ];
    public function class()
    {
        return $this->belongsTo(Classess::class, 'class_id', 'id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
