<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OltController;
use App\Http\Controllers\NapController;
use App\Http\Controllers\Ipv4NetworkController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    ClientController,
    ServiceController,
    InvoiceController,
    PaymentController,
    CompanyController,
    PlanController,
    EmailServerController,
    ClientEmailController
};

Route::prefix('clientes/emails')->group(function () {
    Route::get('/', [ClientEmailController::class, 'index'])->name('clientes.emails.index');
    Route::get('/nuevo', [ClientEmailController::class, 'create'])->name('clientes.emails.create');
    Route::post('/', [ClientEmailController::class, 'store'])->name('clientes.emails.store');

});


/*
|--------------------------------------------------------------------------
| Rutas públicas (auth)
|--------------------------------------------------------------------------
*/
Route::get('/login',  [AuthController::class, 'form'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');
Route::get('/clientes/{cliente}/servicios/nuevo', [\App\Http\Controllers\ServiceController::class, 'createForClient'])
    ->name('clientes.servicios.create');
Route::get('/clientes/mapa', [ClientController::class, 'map'])->name('clientes.map');
Route::get('/clientes/map', [ClientController::class, 'map'])->name('clientes.map');
Route::get('/servicios/mapa', [\App\Http\Controllers\ServiceController::class, 'mapa'])
     ->name('servicios.mapa');

Route::get('/config/email', [EmailServerController::class, 'index'])->name('config.email');
Route::post('/config/email/update', [EmailServerController::class, 'update'])->name('config.email.update');
Route::post('/config/email/test', [EmailServerController::class, 'sendTest'])->name('config.email.test');



/*
|--------------------------------------------------------------------------
| Rutas protegidas
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Home → Panel
    Route::get('/', fn () => redirect()->route('panel'));
    Route::get('/panel', [DashboardController::class, 'index'])->name('panel');

    // Configuración de Empresa
    Route::get('/empresa',        [CompanyController::class, 'edit'])->name('empresa.edit');
    Route::put('/empresa',        [CompanyController::class, 'update'])->name('empresa.update');
    Route::delete('/empresa/logo',[CompanyController::class, 'destroyLogo'])->name('empresa.logo.destroy');

    // Catálogo de planes
    Route::resource('/planes', PlanController::class)->names('planes');

    // Servicios asignados a clientes
    Route::resource('/servicios', ServiceController::class)->names('servicios');

    // CRUDs principales
    Route::resource('/clientes', ClientController::class)->names('clientes')->where(['cliente' => '[0-9]+']);
    Route::resource('/servicios', ServiceController::class)->names('servicios'); // servicios contratados por cliente
    Route::resource('/facturas',  InvoiceController::class)->names('facturas');
    Route::resource('/pagos',     PaymentController::class)->names('pagos');

    // Acción custom: aplicar pago pendiente a una factura
    Route::post('/pagos/{pago}/aplicar', [PaymentController::class, 'apply'])->name('pagos.apply');


// Clientes: vista de mapa
Route::get('/clientes/mapa', [ClientController::class, 'map'])->name('clientes.mapa');
Route::get('/clientes/map',  [ClientController::class, 'map'])->name('clientes.map'); // alias opcional

});

// --- Gestión de red / Routers ---
Route::prefix('routers')->name('routers.')->group(function () {
    Route::get('/',        [RouterController::class, 'index'])->name('index');
    Route::get('/create',  [RouterController::class, 'create'])->name('create');
    Route::post('/',       [RouterController::class, 'store'])->name('store');
    Route::get('/{id}/edit',[RouterController::class, 'edit'])->name('edit');
    Route::put('/{id}',    [RouterController::class, 'update'])->name('update');
    Route::delete('/{id}', [RouterController::class, 'destroy'])->name('destroy');
});


// --- Gestión de red / Redes IPv4 ---
Route::prefix('ipv4')->name('ipv4.')->group(function () {
    Route::get('/',         [Ipv4NetworkController::class, 'index'])->name('index');
    Route::get('/create',   [Ipv4NetworkController::class, 'create'])->name('create');
    Route::post('/',        [Ipv4NetworkController::class, 'store'])->name('store');
    Route::get('/{id}/edit',[Ipv4NetworkController::class, 'edit'])->name('edit');
    Route::put('/{id}',     [Ipv4NetworkController::class, 'update'])->name('update');
    Route::delete('/{id}',  [Ipv4NetworkController::class, 'destroy'])->name('destroy');
});


// --- Gestión de red / Cajas NAP (OLTs y NAPs) ---
Route::prefix('olts')->name('olts.')->group(function () {
    Route::get('/',           [OltController::class, 'index'])->name('index');
    Route::get('/create',     [OltController::class, 'create'])->name('create');
    Route::post('/',          [OltController::class, 'store'])->name('store');
    Route::get('/{id}',       [OltController::class, 'show'])->name('show');
    Route::get('/{id}/edit',  [OltController::class, 'edit'])->name('edit');
    Route::put('/{id}',       [OltController::class, 'update'])->name('update');
    Route::delete('/{id}',    [OltController::class, 'destroy'])->name('destroy');

    // NAPs anidadas por OLT
    Route::get('/{olt}/naps/create',         [NapController::class, 'create'])->name('naps.create');
    Route::post('/{olt}/naps',               [NapController::class, 'store'])->name('naps.store');
    Route::get('/{olt}/naps/{id}/edit',      [NapController::class, 'edit'])->name('naps.edit');
    Route::put('/{olt}/naps/{id}',           [NapController::class, 'update'])->name('naps.update');
    Route::delete('/{olt}/naps/{id}',        [NapController::class, 'destroy'])->name('naps.destroy');
});
