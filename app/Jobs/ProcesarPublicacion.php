<?php

namespace App\Jobs;

use App\Models\TelegramChat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class ProcesarPublicacion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        $nombre = $this->data['nombre'] ?? 'Slime';
        $link = $this->data['link'] ?? '';
        $tags = $this->data['tags'] ?? [];

        try {
            $mensaje = \App\Models\Mensaje::latest()->first();

            if (!$mensaje) {
                \Log::warning('No hay mensajes en la base de datos.');
                return;
            }

            // Armar texto
            $texto = $mensaje->texto ?? '';
            
            $tagsTexto = implode(' ', array_map(fn($t) => "#$t", $tags));

            $texto = str_replace(
                ['{mensaje}', '{link}', '{tags}'],
                [$nombre, $link, $tagsTexto],
                $texto
            );

            $imagen = $mensaje->imagen ?? null;

            // Botones opcionales desde los tags
            $botones = [];
            foreach ($mensaje->botones as $boton) {
                $botones[] = [
                    'text' => $boton->texto,
                    'url' => $boton->url,
                ];
            }

            // Obtener canales
            $canales = \App\Models\TelegramChat::all();

            if ($canales->isEmpty()) {
                \Log::warning('No hay canales disponibles para publicar.');
                return;
            }

            // Construir reply_markup si hay botones
            $replyMarkup = null;
            if (!empty($botones)) {
                $inlineKeyboard = array_map(fn($boton) => [[
                    'text' => $boton['text'],
                    'url' => $boton['url']
                ]], $botones);

                $replyMarkup = json_encode(['inline_keyboard' => $inlineKeyboard]);
            }

            foreach ($canales as $canal) {
                $params = [
                    'chat_id' => $canal->chat_id,
                    'parse_mode' => 'HTML',
                ];

                if ($replyMarkup) {
                    $params['reply_markup'] = $replyMarkup;
                }

                if ($imagen) {
                    $params['photo'] = InputFile::create($imagen);
                    $params['caption'] = $texto;
                    \Telegram::sendPhoto($params);
                } else {
                    $params['text'] = $texto;
                    \Telegram::sendMessage($params);
                }

                \Log::info("Publicando en {$canal->chat_id}: $texto");
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando mensaje a Telegram: ' . $e->getMessage());
        }
    }
}
