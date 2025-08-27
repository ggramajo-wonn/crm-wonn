<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Router;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /** LISTADO */
    public function index(Request $request)
    {
        $q = Service::query()
            ->with(['client','plan']) // NO 'router' para no depender de la relación
            ->latest('id');

        // filtros simples opcionales
        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        if ($request->filled('client_id')) {
            $q->where('client_id', (int) $request->input('client_id'));
        }

        $items = $q->paginate(15)->withQueryString();

        // Mapa rápido id => Router (para mostrar nombre sin relación)
        $routersMap = Router::query()->get()->keyBy('id');

        return view('servicios.index', [
            'items'      => $items,
            'routersMap' => $routersMap,
        ]);
    }

    /** CREAR */
    public function create()
    {
        $clients = Client::orderBy('nombre')->get();
        $planes  = Plan::orderBy('nombre')->get();
        $routers = Router::orderBy('nombre')->get();

        return view('servicios.create', compact('clients','planes','routers'));
    }

    /** GUARDAR */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $service = Service::create($data);

        return redirect()->route('servicios.show', $service)
            ->with('success', 'Servicio creado correctamente.');
    }

    /** VER */
    public function show(Service $service)
    {
        // Cargamos sólo lo que existe en el modelo
        $service->load(['client','plan']);
        $router = $service->router_id ? Router::find($service->router_id) : null;

        return view('servicios.single', [
            'service' => $service,
            'router'  => $router,
        ]);
    }

    /** EDITAR */
    public function edit(Service $service)
    {
        $clients = Client::orderBy('nombre')->get();
        $planes  = Plan::orderBy('nombre')->get();
        $routers = Router::orderBy('nombre')->get();

        return view('servicios.edit', [
            'service' => $service,
            'clients' => $clients,
            'planes'  => $planes,
            'routers' => $routers,
        ]);
    }

    /** ACTUALIZAR */
    public function update(Request $request, Service $service)
    {
        $data = $this->validatedData($request, $service->id);

        $service->update($data);

        return redirect()->route('servicios.show', $service)
            ->with('success', 'Servicio actualizado correctamente.');
    }

    /** ELIMINAR */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio eliminado.');
    }

    /** MAPA (si tu vista espera estos datos) */
    public function mapa()
    {
        $services = Service::query()->select('id','client_id','gps','status','ip')->get();
        $clientes = Client::query()->select('id','nombre')->get()->keyBy('id');
        // Si usás NAPs, cargalas aquí y pasalas a la vista
        return view('servicios.mapa', compact('services','clientes'));
    }

    public function map()
    {
        return $this->mapa();
    }

    /** --------- Helpers --------- */
    private function validatedData(Request $request, $ignoreId = null): array
    {
        // Acepta alias para compatibilidad hacia atrás
        $payload = $request->all();

        if (isset($payload['cliente_id']) && !isset($payload['client_id'])) {
            $payload['client_id'] = $payload['cliente_id'];
        }
        if (isset($payload['precio']) && !isset($payload['price'])) {
            $payload['price'] = $payload['precio'];
        }

        return $request->merge($payload)->validate([
            'client_id' => ['required','integer','exists:clients,id'],
            'plan_id'   => ['nullable','integer','exists:plans,id'],
            'router_id' => ['nullable','integer','exists:routers,id'],
            'price'     => ['required','numeric','min:0'],
            'status'    => ['required','in:activo,suspendido,baja'],
            'ip'        => ['nullable','ip'],
            'gps'       => ['nullable','string'],
        ]);
    }
}
