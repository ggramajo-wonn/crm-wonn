<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PanelController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ClientEmailController;

// Controllers opcionales (se registran solo si existen)
$RouterCtrl      = \App\Http\Controllers\RouterController::class;
$Ipv4Ctrl        = \App\Http\Controllers\Ipv4NetworkController::class;
$OltCtrl         = \App\Http\Controllers\OltController::class;
$ConfigCtrl      = \App\Http\Controllers\ConfigController::class;
$CompanyCtrl     = \App\Http\Controllers\CompanyController::class;

/*
|--------------------------------------------------------------------------
| Home / Panel
|--------------------------------------------------------------------------
*/
Route::get('/', [PanelController::class, 'index'])->name('panel');

/*
|--------------------------------------------------------------------------
| Logout (el layout usa route('logout'))
|   - dejamos POST (seguro) y además GET para dev local (por si el menú no usa form)
|--------------------------------------------------------------------------
*/
Route::post('/logout', function () {
    Auth::guard()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('panel');
})->name('logout');

Route::get('/logout', function () {
    // Soporte en desarrollo si el link dispara GET
    Auth::guard()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('panel');
});

/*
|--------------------------------------------------------------------------
| Servicios
|--------------------------------------------------------------------------
*/
Route::resource('servicios', ServiceController::class);
Route::get('servicios/mapa', [ServiceController::class, 'mapa'])->name('servicios.mapa');

/*
|--------------------------------------------------------------------------
| Clientes > Emails (nombres usados por el layout)
|--------------------------------------------------------------------------
*/
Route::prefix('clientes/emails')->name('clientes.emails.')->group(function () {
    Route::get('/',      [ClientEmailController::class, 'index'])->name('index');
    Route::get('/nuevo', [ClientEmailController::class, 'create'])->name('create');
    Route::post('/',     [ClientEmailController::class, 'store'])->name('store');
});

/*
|--------------------------------------------------------------------------
| Routers (solo si existe controller; si no, stub routers.index)
|--------------------------------------------------------------------------
*/
if (class_exists($RouterCtrl)) {
    Route::resource('routers', $RouterCtrl);
} else {
    Route::get('routers', fn() => redirect()->route('panel'))->name('routers.index');
}

/*
|--------------------------------------------------------------------------
| IPv4 Networks (route('ipv4.index'))
|--------------------------------------------------------------------------
*/
if (class_exists($Ipv4Ctrl)) {
    Route::resource('ipv4', $Ipv4Ctrl)->only(['index','create','store','show','edit','update','destroy']);
} else {
    Route::get('ipv4', fn() => redirect()->route('panel'))->name('ipv4.index');
}

/*
|--------------------------------------------------------------------------
| OLTs (route('olts.index'))
|--------------------------------------------------------------------------
*/
if (class_exists($OltCtrl)) {
    Route::resource('olts', $OltCtrl);
} else {
    Route::get('olts', fn() => redirect()->route('panel'))->name('olts.index');
}

/*
|--------------------------------------------------------------------------
| Config > Email (route('config.email'))
|--------------------------------------------------------------------------
*/
if (class_exists($ConfigCtrl) && method_exists($ConfigCtrl, 'email')) {
    Route::get('config/email', [$ConfigCtrl, 'email'])->name('config.email');
} elseif (class_exists($CompanyCtrl) && method_exists($CompanyCtrl, 'email')) {
    Route::get('config/email', [$CompanyCtrl, 'email'])->name('config.email');
} elseif (class_exists($ConfigCtrl) && method_exists($ConfigCtrl, 'index')) {
    Route::get('config/email', [$ConfigCtrl, 'index'])->name('config.email');
} else {
    Route::get('config/email', fn() => redirect()->route('panel'))->name('config.email');
}

/*
|--------------------------------------------------------------------------
| Fallback (opcional) -> evita 404 en desarrollo
|--------------------------------------------------------------------------
*/
// Route::fallback(fn() => redirect()->route('panel'));
