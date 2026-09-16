<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\EventoController;
use App\Http\Controllers\Api\InscripcionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Publicas
    Route::get('eventos', [EventoController::class, 'index']);
    Route::get('eventos/{evento}', [EventoController::class, 'show']);
    Route::get('categorias', [CategoriaController::class, 'index']);

    Route::post('login', [AuthController::class, 'login']);

    // Protegidas con token
    Route::middleware(['auth:sanctum', 'activo'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('mis-inscripciones', [InscripcionController::class, 'index']);
        Route::post('eventos/{evento}/inscribirse', [InscripcionController::class, 'store'])
            ->middleware('inscripcion.abierta');
    });
});
