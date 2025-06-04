<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TelegramChat;

class TelegramListChats extends Command
{
    protected $signature = 'telegram:list-chats';

    protected $description = 'Listar chats autorizados';

    public function handle()
    {
        $chats = TelegramChat::all();

        if ($chats->isEmpty()) {
            $this->info('No hay chats autorizados.');
            return 0;
        }

        $headers = ['ID', 'Chat ID', 'Título', 'Tipo', 'Creado'];
        $data = $chats->map(fn($chat) => [
            $chat->id,
            $chat->chat_id,
            $chat->title,
            $chat->type,
            $chat->created_at->format('Y-m-d H:i'),
        ])->toArray();

        $this->table($headers, $data);

        return 0;
    }
}
