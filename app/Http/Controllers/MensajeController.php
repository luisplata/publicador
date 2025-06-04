<?php

namespace App\Http\Controllers;

use App\Models\Boton;
use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'texto' => 'required|string',
            'imagen' => 'nullable|url',
            'botones' => 'nullable|array',
            'botones.*.texto' => 'required_with:botones|string',
            'botones.*.url' => 'required_with:botones|url',
        ]);

        $mensaje = Mensaje::create([
            'texto' => $data['texto'],
            'imagen' => $data['imagen'] ?? null,
        ]);

        if (!empty($data['botones'])) {
            foreach ($data['botones'] as $botonData) {
                $mensaje->botones()->create($botonData);
            }
        }

        return response()->json(['message' => 'Mensaje creado correctamente', 'mensaje' => $mensaje->load('botones')], 201);
    }
}
