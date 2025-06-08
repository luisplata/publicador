<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramChat extends Model
{
    protected $fillable = [
        'chat_id',
        'title',
        'type',
    ];

    protected $table = 'telegram_chats';
}
