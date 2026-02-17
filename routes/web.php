<?php

use App\Http\Controllers\PergolaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //phpinfo();
    dump($_SERVER);
    return view('welcome');
});

Route::get('/pergola', [PergolaController::class, 'index']);
Route::post('/pergola/generate', [PergolaController::class, 'generate']);
Route::post('/pergola/describe', [PergolaController::class, 'describe']);
