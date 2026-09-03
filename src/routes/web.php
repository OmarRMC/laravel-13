<?php

use App\Http\Controllers\Admin\CategoriaController as AdminCategoriaController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\Panel\EventoController as PanelEventoController;
use App\Http\Controllers\Panel\InscritoController;
use App\Http\Controllers\Panel\ReporteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EventoController::class, 'index'])->name('home');
Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');


Route::get('/eventos/categoria/{categoria?}', [EventoController::class, 'index'])
    ->name('eventos.categoria');

Route::get('/eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');

Route::middleware(['auth', 'activo'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/mis-inscripciones', [InscripcionController::class, 'index'])
        ->name('inscripciones.index');

    Route::post('/eventos/{evento}/inscribirse', [InscripcionController::class, 'store'])
        ->middleware('inscripcion.abierta')
        ->name('inscripciones.store');

    Route::patch('/eventos/{evento}/inscripcion', [InscripcionController::class, 'cancelar'])
        ->name('inscripciones.cancelar');

    Route::get('/eventos/{evento}/certificado', CertificadoController::class)
        ->name('certificado');
});

Route::middleware(['auth', 'activo', 'can:crear eventos'])
    ->prefix('panel')
    ->name('panel.')
    ->group(function () {

        Route::resource('eventos', PanelEventoController::class)->except('show');

        Route::get('eventos/{evento}/inscritos', [InscritoController::class, 'index'])
            ->name('eventos.inscritos');

        // scopeBindings(): {inscrito} tiene que estar inscrito en {evento}.
        Route::patch('eventos/{evento}/asistencia/{inscrito}', [InscritoController::class, 'asistencia'])
            ->scopeBindings()
            ->name('eventos.asistencia');

        Route::get('eventos/{evento}/reporte/pdf', [ReporteController::class, 'pdf'])
            ->name('eventos.pdf');

        Route::get('eventos/{evento}/reporte/excel', [ReporteController::class, 'excel'])
            ->name('eventos.excel');
    });

Route::middleware(['auth', 'activo', 'can:ver-panel-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('categorias', AdminCategoriaController::class)->except('show');

        // Genera el parametro {usuario}: el metodo del controlador debe
        // recibir $usuario, no $user, o el binding llega null.
        Route::resource('usuarios', AdminUserController::class)
            ->only(['index', 'edit', 'update']);
    });

require __DIR__.'/auth.php';
