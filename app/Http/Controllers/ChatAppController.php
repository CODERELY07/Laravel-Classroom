<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Classess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChatAppController extends Controller
{
    private const HISTORY_LIMIT = 100;

    public function fireMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'class_id' => 'required|integer|exists:classess,id',
            'message' => 'required|string|max:2000',
        ]);

        $class = Classess::findOrFail($data['class_id']);
        Gate::authorize('view', $class);

        $chat = $class->messages()->create([
            'user_id' => $request->user()->id,
            'message' => trim($data['message']),
        ]);
        $chat->setRelation('user', $request->user());

        // A websocket outage should not lose the message, which is already saved.
        try {
            // Unset forces PendingBroadcast to dispatch inside this try block.
            $pending = broadcast(new MessageSent($chat))->toOthers();
            unset($pending);
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json(['chat' => $chat->toChatPayload()]);
    }

    public function getChat(Classess $class): JsonResponse
    {
        Gate::authorize('view', $class);

        $chats = $class->messages()
            ->with('user:id,name,profile')
            ->latest('id')
            ->limit(self::HISTORY_LIMIT)
            ->get()
            ->reverse()
            ->values();

        return response()->json(['chats' => $chats->map->toChatPayload()]);
    }
}
