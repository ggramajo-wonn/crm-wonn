<?php
namespace App\Http\Controllers;

use App\Models\{Company, Client, Service, Invoice, Payment};

class DashboardController extends Controller
{
    public function index()
    {
        $company = Company::first();

        $clientesTotal = Client::count();
        $serviciosTotal = Service::count();
        $serviciosActivos = Service::where('status', 'activo')->count();
        $serviciosSuspendidos = Service::where('status', 'suspendido')->count();

        $facturado = Invoice::sum('total');
        $pagado = Payment::where('status', 'acreditado')->sum('amount');

        return view('panel.index', compact(
            'company',
            'clientesTotal', 'serviciosTotal', 'serviciosActivos', 'serviciosSuspendidos',
            'facturado', 'pagado'
        ));
    }
}
