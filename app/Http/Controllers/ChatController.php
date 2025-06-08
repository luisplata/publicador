<?php

namespace App\Http\Controllers;

use App\Models\TelegramChat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return response()->json(TelegramChat::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string|unique:telegram_chats,chat_id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:group,channel,user',
        ]);

        $chat = TelegramChat::create($request->only(['chat_id', 'title', 'type']));

        return response()->json(['message' => 'Chat agregado', 'chat' => $chat], 201);
    }

    public function destroy(TelegramChat $chat)
    {
        $chat->delete();

        return response()->json(['message' => 'Chat eliminado']);
    }
}
