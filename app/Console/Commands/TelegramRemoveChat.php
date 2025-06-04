<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TelegramChat;

class TelegramRemoveChat extends Command
{
    protected $signature = 'telegram:remove-chat {chat_id}';

    protected $description = 'Elimina un chat por su ID';

    public function handle()
    {
        $chatId = $this->argument('chat_id');

        $chat = TelegramChat::where('chat_id', $chatId)->first();

        if (!$chat) {
            $this->error("No se encontró chat con ID {$chatId}");
            return 1;
        }

        $chat->delete();

        $this->info("Chat con ID {$chatId} eliminado correctamente.");
        return 0;
    }
}
