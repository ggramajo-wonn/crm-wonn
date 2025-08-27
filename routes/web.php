<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Asegura que las rutas de autenticación de Breeze estén cargadas
require __DIR__.'/auth.php';

// Si agregaste un archivo routes/profile.php, descomenta la siguiente línea:
// require __DIR__.'/profile.php';
