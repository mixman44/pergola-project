<?php

use App\Http\Controllers\PergolaController;
use Illuminate\Support\Facades\Route;

// =============================================
// ROUTE RACINE → Redirige directement vers ton app
// =============================================
Route::get('/', function () {
    return redirect('/pergola');
});

// =============================================
// TES ROUTES PERGOLA (inchangées)
// =============================================
Route::get('/pergola', [PergolaController::class, 'index']);
Route::post('/pergola/generate', [PergolaController::class, 'generate']);
Route::post('/pergola/describe', [PergolaController::class, 'describe']);
