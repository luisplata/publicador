<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramChatsTable extends Migration
{
    public function up()
    {
        Schema::create('telegram_chats', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->unique(); // ID del chat (puede ser negativo para grupos/canales)
            $table->string('title')->nullable(); // Nombre del canal/grupo/usuario
            $table->enum('type', ['channel', 'group', 'user']); // Tipo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('telegram_chats');
    }
}
