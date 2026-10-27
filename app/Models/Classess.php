<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Classess extends Model
{
    public const STATUS_ACTIVE = 'ok';
    public const STATUS_ARCHIVED = 'archive';

    protected $table = 'classess';

    protected $fillable = [
        'classname',
        'section',
        'subject',
        'class_code',
        'status',
        'teacher_id',
    ];

    /** Generate a class code that is not used by any other class. */
    public static function generateCode(): string
    {
        do {
            $code = Str::lower(Str::random(7));
        } while (static::where('class_code', $code)->exists());

        return $code;
    }

    /** Classes the given user belongs to, as teacher or student. */
    public function scopeForMember(Builder $query, User $user): Builder
    {
        return $query->whereHas('members', fn ($q) => $q->where('user_id', $user->id));
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function members()
    {
        return $this->hasMany(ClassMember::class, 'class_id');
    }

    public function messages()
    {
        return $this->hasMany(ClassroomChat::class, 'class_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'class_members', 'class_id', 'user_id')
                    ->withPivot('role')
                    ->wherePivot('role', ClassMember::ROLE_TEACHER);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_members', 'class_id', 'user_id')
                    ->withPivot('role')
                    ->wherePivot('role', ClassMember::ROLE_STUDENT);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'class_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'class_id');
    }

    public function membershipFor(User $user): ?ClassMember
    {
        return $this->members()->where('user_id', $user->id)->first();
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /** Stable palette slot (0-5) used to colour the class banner. */
    public function themeIndex(): int
    {
        return $this->id % 6;
    }
}
