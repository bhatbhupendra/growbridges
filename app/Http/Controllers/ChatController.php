<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function show(User $receiver, User $sender)
    {
        $messages = ChatMessage::where(function ($q) use ($receiver, $sender) {
                $q->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver->id);
            })
            ->orWhere(function ($q) use ($receiver, $sender) {
                $q->where('sender_id', $receiver->id)
                ->where('receiver_id', $sender->id);
            })
            ->orderBy('created_at')
            ->get();

        return view('chat.show', compact('receiver', 'sender', 'messages'));
    }

    public function send(Request $request, User $receiver, User $sender)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ChatMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
        ]);

        return back();
    }
}