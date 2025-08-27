<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RouterController extends Controller
{
    /** Listado */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $routers = Router::query()
            ->when($q !== '', function ($w) use ($q) {
                $like = '%' . $q . '%';
                $w->where(function ($s) use ($like) {
                    $s->where('id', 'like', $like)
                      ->orWhere('name', 'like', $like)
                      ->orWhere('ip', 'like', $like);
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        // Contar clientes asociados por router (depende de esquema de services)
        $hasRouterId  = Schema::hasColumn('services', 'router_id');
        $hasRouterCol = Schema::hasColumn('services', 'router'); // string/ID en tu app actual
        foreach ($routers as $r) {
            $count = 0;
            try {
                if ($hasRouterId) {
                    $count = DB::table('services')->where('router_id', $r->id)->count();
                } elseif ($hasRouterCol) {
                    $count = DB::table('services')
                        ->where(function($q) use ($r) {
                            $q->where('router', (string) $r->id);
                        })
                        ->orWhere(function($q) use ($r) {
                            $q->where('router', $r->name);
                        })
                        ->count();
                }
            } catch (\Throwable $e) {
                $count = 0;
            }
            $r->clients_count = $count;
        }

        return view('routers.index', compact('routers', 'q'));
    }

    /** Crear */
    public function create()
    {
        $router = new Router();
        $router->speed_control = 'simple_queues';
        return view('routers.create', compact('router'));
    }

    /** Guardar */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required','string','max:255'],
            'ip'            => ['required','string','max:255'],
            'gps'           => ['nullable','string','max:255'],
            'api_user'      => ['nullable','string','max:255'],
            'api_pass'      => ['nullable','string','max:255'],
            'speed_control' => ['required','in:simple_queues'],
        ]);

        // Campos futuros (vía API): model, version
        $data['model'] = $request->input('model');
        $data['version'] = $request->input('version');

        Router::create($data);

        return redirect()->route('routers.index')->with('ok', 'Router creado correctamente.');
    }

    /** Editar */
    public function edit($id)
    {
        $router = Router::findOrFail($id);
        return view('routers.edit', compact('router'));
    }

    /** Actualizar */
    public function update(Request $request, $id)
    {
        $router = Router::findOrFail($id);

        $data = $request->validate([
            'name'          => ['required','string','max:255'],
            'ip'            => ['required','string','max:255'],
            'gps'           => ['nullable','string','max:255'],
            'api_user'      => ['nullable','string','max:255'],
            'api_pass'      => ['nullable','string','max:255'],
            'speed_control' => ['required','in:simple_queues'],
        ]);

        $router->update($data);

        return redirect()->route('routers.index')->with('ok', 'Router actualizado.');
    }

    /** Eliminar (bloquear si tiene clientes asociados) */
    public function destroy($id)
    {
        $router = Router::findOrFail($id);

        $clients = 0;
        try {
            if (Schema::hasColumn('services','router_id')) {
                $clients = DB::table('services')->where('router_id', $router->id)->count();
            } elseif (Schema::hasColumn('services','router')) {
                $clients = DB::table('services')
                    ->where('router', (string)$router->id)
                    ->orWhere('router', $router->name)
                    ->count();
            }
        } catch (\Throwable $e) {}

        if ($clients > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar: hay ' . $clients . ' cliente(s) asociados.');
        }

        $router->delete();
        return redirect()->route('routers.index')->with('ok', 'Router eliminado.');
    }
}
