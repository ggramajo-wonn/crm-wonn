<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Service;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Company;
use App\Models\Nap;

class ServiceController extends Controller
{
    /** Listado de servicios */
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $services = Service::query()
            ->when($q !== '', function ($qr) use ($q) {
                $like = '%'.$q.'%';
                $qr->where('id', $q);
                if (Schema::hasColumn('services', 'ip')) {
                    $qr->orWhere('ip', 'like', $like);
                }
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('servicios.index', [
            'services' => $services,
            'items'    => $services,
            'q'        => $q,
        ]);
    }

    /** Mapa de servicios (Google Maps) */
    public function map(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $company = class_exists(Company::class) ? Company::first() : null;

        $svcCid     = Schema::hasColumn('services','client_id') ? 'client_id' :
                      (Schema::hasColumn('services','cliente_id') ? 'cliente_id' : null);
        $svcPlanId  = Schema::hasColumn('services','plan_id') ? 'plan_id' : null;
        $svcLatCol  = Schema::hasColumn('services','gps_lat') ? 'gps_lat' : (Schema::hasColumn('services','lat') ? 'lat' : null);
        $svcLngCol  = Schema::hasColumn('services','gps_lng') ? 'gps_lng' : (Schema::hasColumn('services','lng') ? 'lng' : null);
        $svcGpsCol  = Schema::hasColumn('services','gps') ? 'gps' : null;
        $svcIpCol   = Schema::hasColumn('services','ip') ? 'ip' : null;

        $cliLatCol  = Schema::hasColumn('clients','gps_lat') ? 'gps_lat' : (Schema::hasColumn('clients','lat') ? 'lat' : null);
        $cliLngCol  = Schema::hasColumn('clients','gps_lng') ? 'gps_lng' : (Schema::hasColumn('clients','lng') ? 'lng' : null);
        $cliGpsCol  = Schema::hasColumn('clients','gps') ? 'gps' : null;

        $qSelect = ['s.id as id'];
        if ($svcCid)    $qSelect[] = 's.'.$svcCid.' as client_id';
        if ($svcPlanId) $qSelect[] = 's.'.$svcPlanId.' as plan_id';
        if ($svcIpCol)  $qSelect[] = 's.'.$svcIpCol.' as ip';

        $qSelect[] = $svcLatCol ? DB::raw('s.'.$svcLatCol.' as s_lat') : DB::raw('NULL as s_lat');
        $qSelect[] = $svcLngCol ? DB::raw('s.'.$svcLngCol.' as s_lng') : DB::raw('NULL as s_lng');
        $qSelect[] = $svcGpsCol ? DB::raw('s.'.$svcGpsCol.' as s_gps') : DB::raw('NULL as s_gps');

        $qSelect[] = 'c.name as client_name';
        $qSelect[] = 'c.localidad as localidad';
        $qSelect[] = 'c.address as address';
        $qSelect[] = 'c.cel1 as cel1';
        $qSelect[] = 'c.cel2 as cel2';
        if (Schema::hasColumn('clients','saldo')) $qSelect[] = 'c.saldo as saldo';

        $qSelect[] = $cliLatCol ? DB::raw('c.'.$cliLatCol.' as c_lat') : DB::raw('NULL as c_lat');
        $qSelect[] = $cliLngCol ? DB::raw('c.'.$cliLngCol.' as c_lng') : DB::raw('NULL as c_lng');
        $qSelect[] = $cliGpsCol ? DB::raw('c.'.$cliGpsCol.' as c_gps') : DB::raw('NULL as c_gps');

        $qSelect[] = 'p.name as plan_name';
        if (Schema::hasColumn('plans','price')) $qSelect[] = 'p.price as plan_price';

        $qb = DB::table('services as s')
            ->leftJoin('clients as c', 'c.id', '=', 's.'.($svcCid ?? 'client_id'))
            ->leftJoin('plans as p', 'p.id', '=', 's.'.($svcPlanId ?? 'plan_id'))
            ->select($qSelect);

        if ($q !== '') {
            $like = '%'.$q.'%';
            $qb->where(function($w) use ($like, $svcIpCol) {
                $w->where('c.name', 'like', $like)
                  ->orWhere('c.localidad', 'like', $like)
                  ->orWhere('c.address', 'like', $like)
                  ->orWhere('p.name', 'like', $like);
                if ($svcIpCol) $w->orWhere('s.'.$svcIpCol, 'like', $like);
            });
        }

        $rows = collect($qb->limit(3000)->get());

        $services = $rows->map(function ($r) {
            $lat = $this->parseCoord($r->s_lat ?? null);
            $lng = $this->parseCoord($r->s_lng ?? null);
            if (($lat === null || $lng === null) && !empty($r->s_gps)) {
                [$lat,$lng] = $this->splitGps($r->s_gps);
            }
            if (($lat === null || $lng === null)) {
                $lat = $this->parseCoord($r->c_lat ?? null);
                $lng = $this->parseCoord($r->c_lng ?? null);
                if (($lat === null || $lng === null) && !empty($r->c_gps)) {
                    [$lat,$lng] = $this->splitGps($r->c_gps);
                }
            }
            $r->lat = $lat;
            $r->lng = $lng;
            if (!isset($r->saldo)) $r->saldo = 0;
            return $r;
        })->filter(function ($r) {
            return is_numeric($r->lat) && is_numeric($r->lng);
        })->values();

        return view('servicios.mapa', [
            'company'  => $company,
            'services' => $services,
        ]);
    }

    /** Mostrar un servicio (si algún día creamos la vista) */
    public function show($id)
    {
        $service = Service::findOrFail($id);
        $cliente = null;
        if (isset($service->client_id)) {
            $cliente = Client::select('id','name')->find($service->client_id);
        } elseif (method_exists($service, 'client')) {
            $cliente = $service->client;
        }

        // Si no existe la vista, podrías crearla en resources/views/servicios/show.blade.php.
        // Por ahora la dejamos como estaba por compatibilidad.
        return view('servicios.show', [
            'service' => $service,
            'cliente' => $cliente,
        ]);
    }

    /** Crear servicio */
    public function create()
    {
        [$clients, $planes] = $this->formLists();
        $service = new Service();
        $cliente = null;

        return view('servicios.create', [
            'service' => $service,
            'cliente' => $cliente,
            'clients' => $clients,
            'planes'  => $planes,
            'plans'   => $planes,
            'routers' => $this->routersList(),
            'naps'    => Nap::select('id','name','puertos')->orderBy('id')->get(),
        ]);
    }

    /** Editar servicio */
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        [$clients, $planes] = $this->formLists();

        // Resolver cliente del servicio
        $cliente = null;
        if (isset($service->client_id)) {
            $cliente = Client::select('id','name')->find($service->client_id);
        } elseif (method_exists($service, 'client')) {
            $cliente = $service->client;
        }

        // URL de retorno garantizada al menú del cliente
        $returnTo = $cliente && $cliente->id
            ? route('clientes.show', $cliente->id)
            : (url()->previous() ?? route('servicios.index'));

        return view('servicios.edit', [
            'service' => $service,
            'cliente' => $cliente,
            'clients' => $clients,
            'planes'  => $planes,
            'plans'   => $planes,
            'routers' => $this->routersList(),
            'naps'    => Nap::select('id','name','puertos')->orderBy('id')->get(),
            'returnTo'=> $returnTo,
        ]);
    }

    /** Guardar */
    public function store(Request $request)
    {
        $data = $this->validateData($request);
        // Normalizar FTTH y campos dependientes
        $data['ftth'] = $request->boolean('ftth');
        if (!$data['ftth']) { $data['nap_id'] = null; $data['nap_port'] = null; }
        $service = Service::create($data);

        return $this->redirectAfterSave($request, $service, 'Servicio creado correctamente');
    }

    /** Actualizar */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $data = $this->validateData($request);
        // Normalizar FTTH y campos dependientes
        $data['ftth'] = $request->boolean('ftth');
        if (!$data['ftth']) { $data['nap_id'] = null; $data['nap_port'] = null; }
        $service->update($data);

        return $this->redirectAfterSave($request, $service, 'Servicio actualizado');
    }

    /* ---------------------------- helpers ---------------------------- */

    private function formLists(): array
    {
        $clients = Client::query()
            ->select('id','name')
            ->orderBy('name')
            ->limit(2000)
            ->get();

        $planes = Plan::query()
            ->select('id','name','price')
            ->orderBy('name')
            ->limit(2000)
            ->get();

        return [$clients, $planes];
    }

    /** Lista de routers de la empresa (configurable) */
    private function routersList(): array
    {
        $routers = [];

        if (class_exists(Company::class)) {
            $company = Company::first();
            if ($company) {
                if (isset($company->routers) && $company->routers) {
                    $raw = $company->routers;
                    if (is_string($raw)) {
                        $routers = array_values(array_filter(array_map('trim', preg_split('/[\n,;]+/', $raw))));
                    } elseif (is_array($raw)) {
                        $routers = array_values(array_filter(array_map('trim', $raw)));
                    }
                } elseif (isset($company->router) && $company->router) {
                    $routers = [trim($company->router)];
                }
            }
        }

        if (empty($routers)) {
            $cfg = config('network.routers') ?? config('app.routers');
            if (is_array($cfg)) $routers = array_values(array_filter(array_map('trim', $cfg)));
        }

        if (empty($routers)) {
            $env = env('APP_ROUTERS');
            if (is_string($env) && $env !== '') {
                $routers = array_values(array_filter(array_map('trim', preg_split('/[\n,;]+/', $env))));
            }
        }

        return $routers;
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required','integer','exists:clients,id'],
            'plan_id'   => ['required','integer','exists:plans,id'],
            'ip'        => ['nullable','string','max:50'],
            'status'    => ['nullable','string','max:20'],
            'gps'       => ['nullable','string','max:100'],
            'gps_lat'   => ['nullable'],
            'gps_lng'   => ['nullable'],
            'router'    => ['nullable','string','max:100'],
            'ftth'      => ['nullable','boolean'],
            'nap_id'    => ['nullable','integer','exists:naps,id'],
            'nap_port'  => ['nullable','integer','min:1'],
        ]);
    }

    private function splitGps(?string $gps): array
    {
        $gps = trim((string)$gps);
        if ($gps === '') return [null,null];
        $parts = preg_split('/[\s,;]+/', $gps);
        if (count($parts) < 2) return [null,null];
        return [$this->parseCoord($parts[0]), $this->parseCoord($parts[1])];
    }

    private function parseCoord($v): ?float
    {
        if ($v === null) return null;
        if (is_string($v)) {
            $v = trim($v);
            if ($v === '') return null;
            if (preg_match('/^-?\d{1,3},\d+$/', $v)) {
                $v = str_replace(',', '.', $v);
            }
        }
        if (!is_numeric($v)) return null;
        return (float) $v;
    }

    /**
     * Decide a dónde redirigir después de guardar/actualizar
     * - Si existe client_id, volvemos al detalle del cliente
     * - En caso contrario, al índice de servicios
     */
    private function redirectAfterSave(Request $request, Service $service, string $msg)
    {
        $clientId = $service->client_id ?? null;
        if ($clientId) {
            return redirect()
                ->route('clientes.show', $clientId)
                ->with('ok', $msg);
        }

        return redirect()
            ->route('servicios.index')
            ->with('ok', $msg);
    }


/** Eliminar servicio (con chequeos de relaciones) */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        // Si hay datos relacionados, bloquear eliminación
        $groups = [
            'facturas' => ['invoices', 'facturas'],
            'pagos'    => ['payments', 'pagos'],
            'emails'   => ['emails', 'emailLogs', 'email_messages'],
            'mensajes' => ['mensajes', 'messages', 'mensajeria', 'chats'],
            'tickets'  => ['tickets', 'incidencias'],
        ];

        $found = [];
        foreach ($groups as $label => $rels) {
            foreach ($rels as $rel) {
                if (method_exists($service, $rel)) {
                    try {
                        if ($service->{$rel}()->exists()) {
                            $found[] = $label;
                            break;
                        }
                    } catch (\Throwable $e) {
                        // ignorar si no se puede consultar la relación
                    }
                }
            }
        }

        if (!empty($found)) {
            $lista = implode(', ', array_unique($found));
            return redirect()->back()
                ->with('error', 'Servicio con datos en el sistema (' . $lista . '). Eliminá esos datos para poder quitar el servicio.');
        }

        try {
            $service->delete();
            return redirect()->back()->with('ok', 'Servicio eliminado correctamente.');
        } catch (\Throwable $e) {
            // Fallback: si existe columna estado, marcar como eliminado
            if (Schema::hasColumn('services', 'estado')) {
                $service->estado = 'eliminado';
                $service->save();
                return redirect()->back()->with('ok', 'Servicio archivado (no se pudo eliminar físico).');
            }
            return redirect()->back()->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }
}
