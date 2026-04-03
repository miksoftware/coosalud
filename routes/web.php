<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/', fn () => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas autenticadas
Route::middleware('auth')->group(function () {

    // Admin only
    Route::middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::post('/consultas/upload', [ConsultaController::class, 'upload'])->name('consultas.upload');
        Route::get('/consultas/{consulta}/process', [ConsultaController::class, 'process'])->name('consultas.process');
        Route::post('/consultas/{consulta}/process-next', [ConsultaController::class, 'processNext'])->name('consultas.processNext');
    });

    // Ambos roles
    Route::get('/consultas', [ConsultaController::class, 'index'])->name('consultas.index');
    Route::get('/consultas/search', [ConsultaController::class, 'search'])->name('consultas.search');
    Route::get('/consultas/files', [ConsultaController::class, 'files'])->name('consultas.files');
    Route::get('/consultas/{consulta}', [ConsultaController::class, 'show'])->name('consultas.show');
    Route::get('/consultas/{consulta}/export', [ConsultaController::class, 'export'])->name('consultas.export');
});
