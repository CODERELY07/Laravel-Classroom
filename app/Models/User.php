<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STUDENT = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
    ];

    protected $appends = ['avatar_url', 'initial'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function classes()
    {
        return $this->belongsToMany(Classess::class, 'class_members', 'user_id', 'class_id')
                    ->withPivot('role', 'joined_at');
    }

    public function memberships()
    {
        return $this->hasMany(ClassMember::class);
    }

    public function isTeacher(): bool
    {
        return strtolower((string) $this->role) === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return ! $this->isTeacher();
    }

    /** Public URL of the uploaded profile picture, or null when the user has none. */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->profile ? asset('images/'.$this->profile) : null);
    }

    protected function initial(): Attribute
    {
        return Attribute::get(fn () => Str::upper(Str::substr((string) $this->name, 0, 1)));
    }
}
