<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelemetryController;

// recepcao de telemetria enviada pelo esp32
Route::post('/telemetria', [TelemetryController::class, 'receberLeitura']);

// download de limites operacionais pelo esp32
Route::get('/limites/{id}', [TelemetryController::class, 'obterLimites']);
