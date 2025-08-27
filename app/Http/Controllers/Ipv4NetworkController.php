<?php

namespace App\Http\Controllers;

use App\Models\Ipv4Network;
use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Ipv4NetworkController extends Controller
{
    /** Listado */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $networks = Ipv4Network::query()
            ->when($q !== '', function ($w) use ($q) {
                $like = '%' . $q . '%';
                $w->where(function ($s) use ($like) {
                    $s->where('name', 'like', $like)
                      ->orWhere('network', 'like', $like)
                      ->orWhere('cidr', 'like', $like);
                });
            })
            ->orderBy('id', 'asc')
            ->get();

        // Preload routers
        $routersById = Router::query()->get(['id','name'])->keyBy('id');

        // Enriquecer con uso
        foreach ($networks as $n) {
            $n->router_name = $routersById[$n->router_id]->name ?? '';

            [$total, $used] = $this->usageFor($n);
            $n->total_ips = $total;
            $n->used_ips  = $used;
            $n->usage_pct = $total > 0 ? round($used * 100 / $total) : 0; // entero
        }

        return view('ipv4.index', compact('networks', 'q'));
    }

    /** Form crear */
    public function create()
    {
        $network = new Ipv4Network();
        $network->type = 'ESTATICO';
        $routers = Router::orderBy('name')->get();
        return view('ipv4.create', compact('network','routers'));
    }

    /** Guardar */
    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Ipv4Network::create($data);
        return redirect()->route('ipv4.index')->with('ok', 'Red IPv4 creada.');
    }

    /** Editar */
    public function edit($id)
    {
        $network = Ipv4Network::findOrFail($id);
        $routers = Router::orderBy('name')->get();
        return view('ipv4.edit', compact('network','routers'));
    }

    /** Actualizar */
    public function update(Request $request, $id)
    {
        $network = Ipv4Network::findOrFail($id);
        $data = $this->validateData($request);
        $network->update($data);
        return redirect()->route('ipv4.index')->with('ok', 'Red IPv4 actualizada.');
    }

    /** Eliminar */
    public function destroy($id)
    {
        $network = Ipv4Network::findOrFail($id);

        // Evitar borrar si hay servicios con IP dentro del rango
        [$total, $used] = $this->usageFor($network);
        if ($used > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar: hay ' . $used . ' IP(s) en uso en esta red.');
        }

        $network->delete();
        return redirect()->route('ipv4.index')->with('ok', 'Red IPv4 eliminada.');
    }

    /** ===== Helpers ===== */

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'network'  => ['required','ip'],
            'cidr'     => ['required','integer','min:0','max:32'],
            'router_id'=> ['nullable','integer','exists:routers,id'],
            'type'     => ['required','in:ESTATICO'],
        ]);

        return $data;
    }

    /** Calcula [total_usable, used] para una red */
    private function usageFor(Ipv4Network $n): array
    {
        $cidr = (int) $n->cidr;
        $net_long = $this->ipToLong($n->network);
        if ($net_long === null) return [0,0];

        // Rango completo
        $host_bits = 32 - $cidr;
        $size = $host_bits >= 0 ? (1 << $host_bits) : 0;
        $first = $net_long;
        $last  = $net_long + $size - 1;

        // Rango de usables
        if ($cidr <= 30) {
            $usable_total = max(0, $size - 2);
            $usable_first = $first + 1;
            $usable_last  = $last - 1;
        } elseif ($cidr == 31) {
            $usable_total = 2;
            $usable_first = $first;
            $usable_last  = $last;
        } else { // /32
            $usable_total = 1;
            $usable_first = $first;
            $usable_last  = $last;
        }

        // Contar IPs en services.ip (string) que caen en el rango, opcional por router
        $used = 0;
        try {
            $query = DB::table('services')->select('ip', 'router', 'router_id')->whereNotNull('ip');
            $rows = $query->get();
            foreach ($rows as $row) {
                $ip_long = $this->ipToLong($row->ip);
                if ($ip_long === null) continue;
                if ($ip_long < $usable_first || $ip_long > $usable_last) continue;
                // si el network tiene router_id definido, filtramos a ese router
                if (!empty($n->router_id)) {
                    if (Schema::hasColumn('services', 'router_id')) {
                        if ((int)($row->router_id ?? 0) !== (int)$n->router_id) continue;
                    } elseif (Schema::hasColumn('services', 'router')) {
                        if ((string)$row->router !== (string)$n->router_id) {
                            $routerName = optional(\App\Models\Router::find($n->router_id))->name;
                            if ($routerName && (string)$row->router !== $routerName) continue;
                        }
                    }
                }
                $used++;
            }
        } catch (\Throwable $e) {
            $used = 0;
        }

        return [$usable_total, $used];
    }

    private function ipToLong(?string $ip): ?int
    {
        if (!$ip) return null;
        $long = ip2long($ip);
        if ($long === false) return null;
        if ($long < 0) {
            $long = sprintf('%u', $long); // unsigned
        }
        return (int) $long;
    }
}
