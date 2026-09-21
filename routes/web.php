<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\TelemetryController;

// rotas publicas
Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/login', [WebsiteController::class, 'login'])->name('login');

// rotas de autenticacao
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// rotas protegidas para usuarios logados
Route::middleware(['auth'])->group(function () {
    Route::get('/dispositivos', [WebsiteController::class, 'dispositivos'])->name('dispositivos.index');
    Route::get('/relatorios', [WebsiteController::class, 'relatorios'])->name('relatorios.index');
    Route::get('/config', [WebsiteController::class, 'config'])->name('config.index');
    Route::post('/config/atualizar', [AuthController::class, 'updateConfig'])->name('config.update');
    
    // consulta de dados em tempo real para o dashboard
    Route::get('/dispositivos/{id}/realtime', [TelemetryController::class, 'obterDadosRealTime'])->name('dispositivos.realtime');
});