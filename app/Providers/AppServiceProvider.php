<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Company;
use App\Services\ClientBalanceService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra bindings/servicios (si los hubiera).
     */
    public function register(): void
    {
        // ...
    }

    /**
     * Bootstrap de la app:
     * - Comparte $company con TODAS las vistas.
     * - Recalcula y persiste el saldo del cliente cuando se guardan/borran pagos o facturas.
     */
    public function boot(): void
    {
        /**
         * Compartir $company en todas las vistas para evitar "Undefined variable $company".
         */
        View::composer('*', function ($view) {
            $company = null;
            try {
                if (class_exists(Company::class)) {
                    $company = Company::query()->first();
                }
            } catch (\Throwable $e) {
                // Puede fallar si aún no corrieron migraciones; lo ignoramos.
                $company = null;
            }
            $view->with('company', $company);
        });

        /**
         * Helpers para obtener el client_id desde distintos modelos
         * y recalcular el saldo usando ClientBalanceService.
         */
        $resolveClientId = function ($model): ?int {
            if (isset($model->client_id) && $model->client_id)  return (int) $model->client_id;
            if (isset($model->cliente_id) && $model->cliente_id) return (int) $model->cliente_id;

            // Por si existe relación cargada
            try {
                if (method_exists($model, 'client') && $model->client) {
                    return (int) ($model->client->id ?? 0) ?: null;
                }
                if (method_exists($model, 'cliente') && $model->cliente) {
                    return (int) ($model->cliente->id ?? 0) ?: null;
                }
            } catch (\Throwable $e) {
                // relación no cargada; ignorar
            }
            return null;
        };

        $recalc = function ($model) use ($resolveClientId) {
            try {
                $cid = $resolveClientId($model);
                if ($cid) {
                    ClientBalanceService::recalc($cid);
                }
            } catch (\Throwable $e) {
                Log::error('Error recalculando saldo', [
                    'model' => get_class($model),
                    'id'    => $model->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        };

        /**
         * Enganchar eventos de Payment/Invoice.
         * Se encapsula en try/catch y class_exists para que no falle durante migraciones.
         */
        try {
            if (class_exists(Payment::class)) {
                Payment::saved($recalc);
                Payment::deleted($recalc);
            }
            if (class_exists(Invoice::class)) {
                Invoice::saved($recalc);
                Invoice::deleted($recalc);
            }
        } catch (\Throwable $e) {
            // Ignorar errores de boot si aún no están listas las tablas.
        }
    }
}
