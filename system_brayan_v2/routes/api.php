<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\EncomiendaEntregaController;
use App\Http\Controllers\Api\v1\EncomiendaEntregaDomicilioController;
use App\Http\Controllers\Api\v1\EncomiendaRetornoController;
use App\Http\Controllers\Api\frontend\RastreoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);

        Route::post('encomiendas/entrega', [EncomiendaEntregaController::class, 'entregar']);
        Route::post('encomiendas/entrega-domicilio', [EncomiendaEntregaDomicilioController::class, 'entregar']);
        Route::post('encomiendas/retorno', [EncomiendaRetornoController::class, 'retornar']);
    });
});
// {url}/api/frontend/tracking?code=BB-001
Route::prefix('frontend')->group(function () {
    Route::get('tracking', [RastreoController::class, 'show']);
});