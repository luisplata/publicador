<?php
// database/migrations/xxxx_xx_xx_xxxxxx_alter_chat_id_bigint_in_telegram_chats.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('telegram_chats', function (Blueprint $table) {
            $table->string('chat_id', 40)->change();
        });
    }

    public function down()
    {
        Schema::table('telegram_chats', function (Blueprint $table) {
            $table->integer('chat_id')->change();
        });
    }
};