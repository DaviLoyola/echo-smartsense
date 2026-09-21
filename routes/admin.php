<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDispositivoController;

// dashboard administrativo
Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// crud de usuarios
Route::get('/criar', [AdminController::class, 'index'])->name('admin.usuarios.criar');
Route::post('/usuarios/salvar', [AdminController::class, 'salvar'])->name('admin.usuarios.salvar');
Route::post('/usuarios/excluir/{id}', [AdminController::class, 'excluir'])->name('admin.usuarios.excluir');

// gestao de motobombas
Route::get('/motobombas', [AdminDispositivoController::class, 'index'])->name('admin.dispositivos.criar');
Route::post('/motobombas/salvar', [AdminDispositivoController::class, 'salvar'])->name('admin.dispositivos.salvar');
Route::post('/motobombas/excluir/{id}', [AdminDispositivoController::class, 'excluir'])->name('admin.dispositivos.excluir');
