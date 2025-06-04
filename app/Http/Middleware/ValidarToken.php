<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidarToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // Puedes mover este token a .env si lo deseas
        $tokenEsperado = env('TELEGRAM_TOKEN', 'mi_token_secreto_super_seguro');

        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token de autorización faltante.'], 401);
        }

        $token = substr($authHeader, 7); // Remueve "Bearer "

        if ($token !== $tokenEsperado) {
            return response()->json(['error' => 'Token inválido.'], 403);
        }

        return $next($request);
    }
}
