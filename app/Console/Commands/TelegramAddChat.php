<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TelegramChat;

class TelegramAddChat extends Command
{
    protected $signature = 'telegram:add-chat 
                            {chat_id : ID del chat (ejemplo: -1002699198618)} 
                            {title : Nombre del chat} 
                            {type : Tipo (channel, group, user)}';

    protected $description = 'Agregar un chat autorizado para publicar mensajes';

    public function handle()
    {
        $chatId = $this->argument('chat_id');
        $title = $this->argument('title');
        $type = $this->argument('type');

        if (!in_array($type, ['channel', 'group', 'user'])) {
            $this->error('Tipo inválido. Debe ser: channel, group o user.');
            return 1;
        }

        $chat = TelegramChat::updateOrCreate(
            ['chat_id' => $chatId],
            ['title' => $title, 'type' => $type]
        );

        $this->info("Chat guardado: ID={$chat->chat_id}, Title={$chat->title}, Type={$chat->type}");

        return 0;
    }
}
