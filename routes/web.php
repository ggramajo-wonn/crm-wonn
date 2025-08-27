<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\ClientEmailController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmailServerController;

/*
|--------------------------------------------------------------------------
| Web Routes (WONN)
|--------------------------------------------------------------------------
| Mantiene rutas en español y nombres consistentes con los controladores
| enviados en app.zip. Protegemos todas las secciones bajo 'auth'.
*/

// Portada / Panel
Route::get('/', [PanelController::class, 'index'])->name('panel');

// Autenticación (Breeze)
require __DIR__.'/auth.php';

// Área protegida
Route::middleware(['auth'/*, 'verified'*/])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clientes
    Route::resource('/clientes', ClientController::class);
    Route::get('/clientes/mapa', [ClientController::class, 'map'])->name('clientes.map');
    Route::get('/clientes/next-id', [ClientController::class, 'nextClientId'])->name('clientes.next');

    // Prospectos (bajo grupo clientes.*)
    Route::get('/clientes/prospectos', [ProspectController::class, 'index'])->name('clientes.prospectos.index');
    Route::get('/clientes/prospectos/crear', [ProspectController::class, 'create'])->name('clientes.prospectos.create');
    Route::post('/clientes/prospectos', [ProspectController::class, 'store'])->name('clientes.prospectos.store');
    Route::post('/clientes/prospectos/{id}/activar', [ProspectController::class, 'activate'])->name('clientes.prospectos.activate');

    // Emails de clientes
    Route::get('/clientes/emails', [ClientEmailController::class, 'index'])->name('clientes.emails.index');
    Route::get('/clientes/emails/crear', [ClientEmailController::class, 'create'])->name('clientes.emails.create');
    Route::post('/clientes/emails', [ClientEmailController::class, 'store'])->name('clientes.emails.store');

    // Servicios
    Route::resource('/servicios', ServiceController::class);
    Route::get('/servicios/mapa', [ServiceController::class, 'map'])->name('servicios.map');

    // Ventas: Facturas
    Route::resource('/facturas', InvoiceController::class);

    // Finanzas: Pagos
    Route::resource('/pagos', PaymentController::class)->only(['index','create','store','destroy']);

    // Planes (Gestión de red)
    Route::resource('/planes', PlanController::class);

    // Configuración > Empresa
    Route::get('/empresa', [CompanyController::class, 'edit'])->name('empresa.edit');
    Route::patch('/empresa', [CompanyController::class, 'update'])->name('empresa.update');
    Route::delete('/empresa/logo', [CompanyController::class, 'destroyLogo'])->name('empresa.logo.destroy');

    // Configuración > Servidor Emails
    Route::get('/config/email', [EmailServerController::class, 'index'])->name('config.email');
    Route::post('/config/email/test', [EmailServerController::class, 'sendTest'])->name('config.email.test');
});
