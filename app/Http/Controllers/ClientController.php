<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Company;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim((string) $request->get('q', ''));
        $sort = $request->get('sort', 'id');
        $dir  = strtolower($request->get('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSort = ['id','name','localidad','status','saldo'];
        if (! in_array($sort, $allowedSort, true)) {
            $sort = 'id';
        }

        $query = Client::query();

        // Excluir prospectos del listado
        if (Schema::hasColumn('clients', 'is_prospect')) {
            $query->where(function($w){
                $w->whereNull('is_prospect')->orWhere('is_prospect', false);
            });
        }

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                  ->orWhere('email', 'like', $like)
                  ->orWhere('localidad', 'like', $like)
                  ->orWhere('address', 'like', $like)
                  ->orWhere('cel1', 'like', $like)
                  ->orWhere('cel2', 'like', $like);
            });
        }

        if ($sort === 'saldo' && !Schema::hasColumn('clients', 'saldo')) {
            $sort = 'id';
        }

        $clients = $query->orderBy($sort, $dir)
                         ->paginate(25)
                         ->withQueryString();

        return view('clientes.index', [
            'clients' => $clients,
            'items'   => $clients,
            'q'       => $q,
            'sort'    => $sort,
            'dir'     => $dir,
        ]);
    }

    public function create()
    {
        $nextId = $this->nextClientId();

        $client = new Client();
        return view('clientes.create', compact('client'));
    }

    /** Crear cliente (acepta ID manual opcional) */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id'        => ['nullable','integer','min:1','unique:clients,id'],
            'name'      => ['required','string','max:255'],
            'dni'       => ['nullable','string','max:50'],
            'email'     => ['nullable','email','max:255'],
            'cel1'      => ['nullable','string','max:50'],
            'cel2'      => ['nullable','string','max:50'],
            'address'   => ['nullable','string','max:255'],
            'localidad' => ['nullable','string','max:100'],
            'cp'        => ['nullable','string','max:20'],
            'gps'       => ['nullable','string','max:100'],
            'status'    => ['nullable','string','max:20'],
        ]);

        $client = new \App\Models\Client();
        if (!empty($data['id'])) {
            $client->id = $data['id'];
            unset($data['id']);
        }
        $client->fill($data);
        $client->save();

        return redirect()->route('clientes.show', $client->id)->with('ok', 'Cliente creado');
    }
    


    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('clientes.edit', [
            'client'  => $client,
            'cliente' => $client,
            'item'    => $client,
        ]);
    }

    public function show($id)
    {
        $client  = Client::findOrFail($id);
        $company = class_exists(Company::class) ? Company::first() : null;

        return view('clientes.show', [
            'client'  => $client,
            'cliente' => $client,
            'item'    => $client,
            'company' => $company,
        ]);
    }

    public function map(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $company = class_exists(Company::class) ? Company::first() : null;

        // Selección de columnas (incluye 'saldo' si existe)
        $select = ['id','name','localidad','address','gps','gps_lat','gps_lng','cel1','cel2'];
        if (Schema::hasColumn('clients', 'saldo')) {
            $select[] = 'saldo';
        }

        $clients = Client::query()
            ->select($select)
            ->when($q !== '', function ($qb) use ($q) {
                $like = '%'.$q.'%';
                $qb->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                      ->orWhere('localidad', 'like', $like)
                      ->orWhere('address', 'like', $like)
                      ->orWhere('cel1', 'like', $like)
                      ->orWhere('cel2', 'like', $like);
                });
            })
            ->limit(2000)
            ->get();

        // Normalizar coordenadas
        $clients->transform(function ($c) {
            $lat = $this->parseCoord($c->gps_lat ?? null);
            $lng = $this->parseCoord($c->gps_lng ?? null);
            if (($lat === null || $lng === null) && !empty($c->gps)) {
                [$lat,$lng] = $this->splitGps($c->gps);
            }
            $c->lat = $lat; $c->lng = $lng;

            // Asegurar que 'saldo' exista aunque no haya columna
            if (!isset($c->saldo)) {
                $c->saldo = 0;
            }
            return $c;
        });

        // Fallback coordenadas desde services si faltan
        $needsFallback = $clients->contains(fn($c) => ($c->lat === null || $c->lng === null));

        if ($needsFallback && Schema::hasTable('services')) {
            $svcCid    = Schema::hasColumn('services','client_id') ? 'client_id' : (Schema::hasColumn('services','cliente_id') ? 'cliente_id' : null);
            $svcLatCol = Schema::hasColumn('services','gps_lat') ? 'gps_lat' : (Schema::hasColumn('services','lat') ? 'lat' : null);
            $svcLngCol = Schema::hasColumn('services','gps_lng') ? 'gps_lng' : (Schema::hasColumn('services','lng') ? 'lng' : null);
            $svcGpsCol = Schema::hasColumn('services','gps')     ? 'gps'     : null;

            if ($svcCid && ($svcLatCol || $svcLngCol || $svcGpsCol)) {
                $rows = DB::table('services')
                    ->select([
                        $svcCid.' as client_id',
                        $svcLatCol ? DB::raw($svcLatCol.' as lat') : DB::raw('NULL as lat'),
                        $svcLngCol ? DB::raw($svcLngCol.' as lng') : DB::raw('NULL as lng'),
                        $svcGpsCol ? DB::raw($svcGpsCol.' as gps') : DB::raw('NULL as gps'),
                    ])
                    ->whereIn($svcCid, $clients->pluck('id')->all())
                    ->orderBy($svcCid)
                    ->get()
                    ->groupBy('client_id');

                $clients->transform(function ($c) use ($rows) {
                    if ($c->lat !== null && $c->lng !== null) return $c;
                    $r = $rows->get($c->id);
                    if ($r && isset($r[0])) {
                        $lat = $this->parseCoord($r[0]->lat ?? null);
                        $lng = $this->parseCoord($r[0]->lng ?? null);
                        if (($lat === null || $lng === null) && !empty($r[0]->gps)) {
                            [$lat,$lng] = $this->splitGps($r[0]->gps);
                        }
                        $c->lat = $lat; $c->lng = $lng;
                    }
                    return $c;
                });
            }
        }

        $clients = $clients->filter(fn($c) => is_numeric($c->lat) && is_numeric($c->lng))->values();

        return view('clientes.mapa', compact('company','clients'));
    }

    private function splitGps(?string $gps): array
    {
        $gps = trim((string) $gps);
        if ($gps === '') return [null, null];
        $parts = preg_split('/[\s,;]+/', $gps);
        if (count($parts) < 2) return [null, null];
        return [$this->parseCoord($parts[0]), $this->parseCoord($parts[1])];
    }

    private function parseCoord($v): ?float
    {
        if ($v === null) return null;
        if (is_string($v)) {
            $v = trim($v);
            if ($v === '') return null;
            if (preg_match('/^-?\d{1,3},\d+$/', $v)) $v = str_replace(',', '.', $v);
        }
        if (!is_numeric($v)) return null;
        return (float) $v;
    }

    
    /** Actualizar cliente (permite cambiar el ID y re-referenciar servicios) */
    public function update(Request $request, $id)
    {
        $client = \App\Models\Client::findOrFail($id);

        $data = $request->validate([
            'id'        => ['nullable','integer','min:1','unique:clients,id,'.$client->id],
            'name'      => ['required','string','max:255'],
            'dni'       => ['nullable','string','max:50'],
            'email'     => ['nullable','email','max:255'],
            'cel1'      => ['nullable','string','max:50'],
            'cel2'      => ['nullable','string','max:50'],
            'address'   => ['nullable','string','max:255'],
            'localidad' => ['nullable','string','max:100'],
            'cp'        => ['nullable','string','max:20'],
            'gps'       => ['nullable','string','max:100'],
            'status'    => ['nullable','string','max:20'],
        ]);

        $oldId = $client->id;
        $newId = $data['id'] ?? $oldId;
        unset($data['id']);

        return DB::transaction(function () use ($client, $data, $oldId, $newId) {
            $client->fill($data);

            if ($newId != $oldId) {
                // Cambiar la PK del cliente
                $client->id = $newId;
                $client->save();

                // Actualizar referencias en tablas relacionadas
                if (Schema::hasTable('services') && Schema::hasColumn('services', 'client_id')) {
                    DB::table('services')->where('client_id', $oldId)->update(['client_id' => $newId]);
                }
            } else {
                $client->save();
            }

            return redirect()->route('clientes.show', $client->id)->with('ok', 'Cliente actualizado');
        });
    }
    
private function nextClientId(): int
    {
        try {
            $row = DB::selectOne("SHOW TABLE STATUS LIKE 'clients'");
            if ($row && isset($row->Auto_increment)) {
                return (int) $row->Auto_increment;
            }
        } catch (\Throwable $e) {
            // ignore and fallback
        }
        $max = DB::table('clients')->max('id');
        return (int) ($max ?? 0) + 1;
    }
    


public function destroy($id)
    {
        $client = Client::findOrFail($id);

        // Revisar si tiene datos relacionados. Si tiene, NO eliminar.
        $relations = [
            'servicios' => ['services', 'servicios'],
            'facturas'  => ['facturas', 'invoices'],
            'pagos'     => ['pagos', 'payments'],
            'emails'    => ['emails', 'emailLogs', 'emailsEnviados', 'emails_enviados', 'email_messages'],
            'mensajes'  => ['mensajes', 'messages', 'mensajeria', 'chats'],
        ];

        $found = [];
        foreach ($relations as $label => $rels) {
            foreach ($rels as $rel) {
                if (method_exists($client, $rel)) {
                    try {
                        if ($client->{$rel}()->exists()) {
                            $found[] = $label;
                            break; // ya marcamos este grupo
                        }
                    } catch (\Throwable $e) {
                        // ignorar si la relación no se puede consultar
                    }
                }
            }
        }

        if (!empty($found)) {
            $lista = implode(', ', array_unique($found));
            return redirect()->back()
                ->with('error', 'Cliente con datos en el sistema (' . $lista . '). Eliminá los mismos para poder quitar al cliente.');
        }

        try {
            $client->delete();
            $msg = 'Cliente eliminado correctamente.';
        } catch (\Throwable $e) {
            // Si no se puede borrar por restricciones, lo archivamos si existe 'estado'
            if (Schema::hasColumn('clients', 'estado')) {
                $client->estado = 'eliminado';
                $client->save();
                $msg = 'Cliente archivado (no se pudo eliminar físico).';
            } else {
                $err = 'No se pudo eliminar: ' . $e->getMessage();
                if (Route::has('clientes.index')) {
                    return redirect()->route('clientes.index')->with('error', $err);
                }
                return redirect('/clientes')->with('error', $err);
            }
        }

        // Redirigir SIEMPRE al listado (evita 404 /clientes/{id} después de borrar)
        if (Route::has('clientes.index')) {
            return redirect()->route('clientes.index')->with('ok', $msg);
        }
        return redirect('/clientes')->with('ok', $msg);
    }
}
