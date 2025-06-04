<?php

use App\Http\Controllers\MensajeController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\ValidarToken::class])->group(function () {
    Route::post('/publicar', [WebhookController::class, 'handle']);

    Route::get('/chats', [ChatController::class, 'index']);  // Listar chats
    Route::post('/chats', [ChatController::class, 'store']);  // Agregar chat
    Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);  // Eliminar chat

    Route::post('/mensajes', [MensajeController::class, 'store']);
});
