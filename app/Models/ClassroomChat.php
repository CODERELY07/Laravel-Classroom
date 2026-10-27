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

    public function classroom()
    {
        return $this->belongsTo(Classess::class, 'class_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Shape sent to the browser, both over HTTP and the websocket. */
    public function toChatPayload(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'created_at' => $this->created_at?->toIso8601String(),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'initial' => $this->user->initial,
                'avatar_url' => $this->user->avatar_url,
            ],
        ];
    }
}
