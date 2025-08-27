<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProspectController extends Controller
{
    /** Listado de prospectos */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $query = Client::query();

        // Filtrar solo prospectos
        if (Schema::hasColumn('clients', 'is_prospect')) {
            $query->where('is_prospect', true);
        } elseif (Schema::hasColumn('clients', 'estado')) {
            $query->whereIn('estado', ['Prospecto', 'prospecto', 'PROSPECTO']);
        }

        // Búsqueda
        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($w) use ($like) {
                foreach (['id','dni','documento','name','nombre','email'] as $col) {
                    if (Schema::hasColumn('clients', $col)) {
                        $w->orWhere($col, 'like', $like);
                    }
                }
            });
        }

        $prospectos = $query->orderByDesc('id')->paginate(20);

        return view('clientes.prospectos.index', compact('prospectos', 'q'));
    }

    /** Formulario de nuevo prospecto */
    public function create()
    {
        return view('clientes.prospectos.create');
    }

    /** Guardar prospecto */
    public function store(Request $request)
    {
        // Validaciones básicas (solo de campos presentes)
        $request->validate([
            'dni'    => ['nullable','string','max:50'],
            'name'   => ['nullable','string','max:255'],
            'nombre' => ['nullable','string','max:255'],
            'email'  => ['nullable','email','max:255'],
        ]);

        // Mapeo de campos esperados -> columnas reales si existen
        $input = [
            'dni'        => $request->input('dni'),
            'name'       => $request->input('name', $request->input('nombre')),
            'cel1'       => $request->input('cel1', $request->input('cel')),
            'cel2'       => $request->input('cel2'),
            'email'      => $request->input('email'),
            'direccion'  => $request->input('direccion'),
            'localidad'  => $request->input('localidad'),
            'cp'         => $request->input('cp'),
            'gps'        => $request->input('gps'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $data = [];
        foreach ($input as $col => $val) {
            if ($val !== null && Schema::hasColumn('clients', $col)) {
                $data[$col] = $val;
            }
        }

        // Marcar como prospecto
        if (Schema::hasColumn('clients', 'is_prospect')) {
            $data['is_prospect'] = true;
        } elseif (Schema::hasColumn('clients', 'estado')) {
            $data['estado'] = 'Prospecto';
        }

        // Si no existe la columna 'name' pero tenés 'nombre', aseguremos que alguno tenga valor
        if (Schema::hasColumn('clients','name') && empty($data['name'])) {
            $data['name'] = $request->input('nombre') ?: 'Prospecto';
        }
        if (Schema::hasColumn('clients','nombre') && empty($data['nombre'])) {
            $data['nombre'] = $request->input('name') ?: 'Prospecto';
        }

        // Inserción defensiva (evita NOT NULLs faltantes)
        $client = DB::table('clients')->insertGetId($data);

        return redirect()->route('clientes.prospectos.index')->with('ok', 'Prospecto creado. Ahora podés cargar un servicio o activarlo.');
    }

    /**
     * Activar un prospecto como cliente:
     * - is_prospect = false (si existe) / estado = 'activo' (si existe)
     * - servicios del cliente: status/estado = 'ACTIVO' y activo = 1, solo en columnas existentes
     */
    public function activate($id)
    {
        $client = Client::findOrFail($id);

        DB::transaction(function () use ($client) {

            // 1) El cliente deja de ser prospecto
            if (Schema::hasColumn('clients', 'is_prospect')) {
                $client->is_prospect = false;
            }
            if (Schema::hasColumn('clients', 'estado')) {
                $client->estado = 'activo';
            }
            $client->save();

            // 2) Activar servicios del cliente (solo columnas existentes)
            $updates = [];
            if (Schema::hasColumn('services', 'status'))  $updates['status']  = 'ACTIVO';
            if (Schema::hasColumn('services', 'estado'))  $updates['estado']  = 'ACTIVO';
            if (Schema::hasColumn('services', 'activo'))  $updates['activo']  = 1;

            if (!empty($updates)) {
                DB::table('services')->whereNotNull('client_id')->where('client_id', $client->id)->update($updates);
            }
        });

        return redirect()->back()->with('ok', 'Prospecto activado como cliente y servicios actualizados.');
    }
}
