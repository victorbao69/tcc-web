<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PedidoApiController;
use App\Http\Controllers\Api\ProdutoApiController;
use Illuminate\Support\Facades\Route;

// API do app mobile (só as telas de CLIENTE do site).
// O Laravel já coloca o prefixo /api; aqui acrescentamos a versão: /api/v1/...
// O name('api.') evita conflito com os nomes das rotas do site (ex: pedidos.destroy).
Route::prefix('v1')->name('api.')->group(function () {

    // ---- Rotas públicas (sem token) ----
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::apiResource('produtos', ProdutoApiController::class)->only(['index']);

    // ---- Rotas protegidas: precisam de "Authorization: Bearer <token>" ----
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);

        // Conta do cliente logado
        Route::get('/me', [AuthApiController::class, 'me']);
        Route::put('/me', [AuthApiController::class, 'update']);
        Route::delete('/me', [AuthApiController::class, 'destroy']);

        // Pedidos (listar, finalizar compra, cancelar)
        Route::apiResource('pedidos', PedidoApiController::class)->only(['index', 'store', 'destroy']);
    });
});
