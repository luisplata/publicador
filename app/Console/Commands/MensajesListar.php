<?php

namespace App\Console\Commands;

use App\Models\Mensaje;
use Illuminate\Console\Command;

class MensajesListar extends Command
{
    protected $signature = 'mensajes:listar';
    protected $description = 'Listar mensajes con sus botones';

    public function handle()
    {
        $mensajes = Mensaje::with('botones')->get();

        foreach ($mensajes as $mensaje) {
            $this->info("ID: {$mensaje->id}");
            $this->line("Texto: {$mensaje->texto}");
            $this->line("Imagen: {$mensaje->imagen}");
            $this->line('Botones:');
            foreach ($mensaje->botones as $boton) {
                $this->line(" - {$boton->texto}: {$boton->url}");
            }
            $this->line('---');
        }

        return 0;
    }
}
