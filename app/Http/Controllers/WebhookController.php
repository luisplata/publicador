<?php

namespace App\Http\Controllers;

use App\Jobs\ProcesarPublicacion;
use App\Models\Mensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|required|string',
            'link' => 'sometimes|required|url',
            'tags' => 'sometimes|array',
            'tags.*' => 'string',
        ]);

        try {
            ProcesarPublicacion::dispatch([
                'nombre' => $data['nombre'] ?? 'Slime',
                'link' => $data['link'] ?? '',
                'tags' => $data['tags'] ?? [],
            ]);

            Log::info('Job ProcesarPublicacion despachado correctamente.');
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Error al manejar el webhook: ' . $e->getMessage());
            return response()->json(['status' => 'error']);
        }
    }
}
