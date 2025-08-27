<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Company;

class PanelController extends Controller
{
    public function index(Request $request)
    {
        $clientesTotal         = Client::query()->count();
        $serviciosTotal        = Service::query()->count();
        $serviciosActivos      = Service::query()->where('status', 'activo')->count();
        $serviciosSuspendidos  = Service::query()->where('status', 'suspendido')->count();

        // Facturado y Pagado (busca columnas comunes; si no existen, devuelve 0)
        $facturado = $this->sumFirstAvailable((new Invoice)->getTable(), [
            'total', 'monto', 'amount', 'importe', 'total_amount', 'monto_total',
        ]);

        $pagado = $this->sumFirstAvailable((new Payment)->getTable(), [
            'amount', 'monto', 'importe', 'total',
        ]);

        $companyName = null;
        try {
            if (class_exists(Company::class)) {
                $company = Company::query()->first();
                $companyName = $company->name ?? $company->nombre ?? null;
            }
        } catch (\Throwable $e) { /* ignorar si no existe tabla */ }

        return view('panel.index', compact(
            'clientesTotal',
            'serviciosTotal',
            'serviciosActivos',
            'serviciosSuspendidos',
            'facturado',
            'pagado',
            'companyName'
        ));
    }

    private function sumFirstAvailable(string $table, array $candidates): float
    {
        foreach ($candidates as $col) {
            try {
                if (Schema::hasColumn($table, $col)) {
                    return (float) (DB::table($table)->sum($col) ?? 0);
                }
            } catch (\Throwable $e) {
                // si la tabla no existe o está sin migrar, continuar
            }
        }
        return 0.0;
    }
}
