<?php

namespace App\Events;

use App\Models\ClassroomChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Broadcast immediately (no queue worker needed) on the class's private
 * channel, so only members of that class receive the message.
 */
class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public ClassroomChat $chat)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('classroom.'.$this->chat->class_id)];
    }

    public function broadcastWith(): array
    {
        return $this->chat->toChatPayload();
    }
}
