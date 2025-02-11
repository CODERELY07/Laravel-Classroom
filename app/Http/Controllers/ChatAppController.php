<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\ClassroomChat;
use Illuminate\Http\Request;

class ChatAppController extends Controller
{
    public function fireMessage(Request $request)
    {
        $validateData = $request->validate([
            'sender' => 'required',
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classess,id',
            'message' => 'required|string'
        ]);
    
       
    
        $chatMessage = ClassroomChat::create($validateData);
    
  
        $chats = ClassroomChat::with('user')->where('class_id', $request->class_id)
        ->orderBy('created_at', 'asc')
        ->get();
        $user =  ClassroomChat::with('user')->where('class_id', $request->class_id)->where('user_id', $request->user_id)->first();
        MessageSent::dispatch($validateData['sender'], $validateData['message'],
        $user->user);

        return response()->json([
            'message' => $chatMessage->message,
            'sender' => $request->sender,
            'user_id' => $request->user_id,
            'class_id' => $request->class_id,
            'chats' => $chats 
        ]);
    }
    
    public function getChat($classID)
    {
        
        $chats = ClassroomChat::with('user')->where('class_id', $classID)
                ->orderBy('created_at', 'asc')
                ->get();
    
        return response()->json([
            'chats' => $chats
        ]);
    }
}
