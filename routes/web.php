<?php

use App\Http\Controllers\BebidaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas de Perfil (Generadas por Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Rutas Administrativas del Sistema de Bebidas (Fase 1)
|--------------------------------------------------------------------------
| Protegidas con Autenticación ('auth') y Rol de Administrador ('role:admin')
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Controlador de Recurso Restringido a las 7 acciones RESTful
        Route::resource('bebidas', BebidaController::class)->parameters([
            'bebidas' => 'bebida:slug', // Búsqueda implícita por slug en lugar de ID
        ]);

        // Listado administrativo de las facturas emitidas en el sistema.
        Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    });

Route::middleware(['auth'])->prefix('carrito')->name('carrito.')->group(function () {
    Route::get('/', [CarritoController::class, 'index'])->name('index');
    Route::post('/agregar/{bebida}', [CarritoController::class, 'agregar'])->name('agregar');
    Route::patch('/actualizar/{bebida}', [CarritoController::class, 'actualizar'])->name('actualizar');
    Route::delete('/eliminar/{bebida}', [CarritoController::class, 'eliminar'])->name('eliminar');
    Route::post('/vaciar', [CarritoController::class, 'vaciar'])->name('vaciar');
});

Route::middleware(['auth'])->group(function () {
    // Vista principal del catálogo para el mesero
    Route::get('/pos', [BebidaController::class, 'pos'])->name('pos.index');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store');
    Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show');
});

require __DIR__.'/auth.php';
