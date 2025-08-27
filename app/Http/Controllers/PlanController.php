<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        // Contadores de clientes activos/suspendidos por plan (a través de services)
        $items = Plan::query()
            ->withCount([
                'services as activos_count'     => fn($q) => $q->where('status','activo'),
                'services as suspendidos_count' => fn($q) => $q->where('status','suspendido'),
            ])
            ->orderBy('name')
            ->paginate(12);

        return view('planes.index', compact('items'));
    }

    public function create()
    {
        $plan = new Plan();
        return view('planes.create', compact('plan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'mb_down'     => 'nullable|integer|min:0',
            'mb_up'       => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Plan::create($data);
        return redirect()->route('planes.index')->with('ok','Plan creado.');
    }

    public function edit(Plan $plane)
    {
        return view('planes.edit', ['plan' => $plane]);
    }

    public function update(Request $request, Plan $plane)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'mb_down'     => 'nullable|integer|min:0',
            'mb_up'       => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $plane->update($data);
        return redirect()->route('planes.index')->with('ok','Plan actualizado.');
    }

    public function destroy(Plan $plane)
    {
        // Puedes impedir borrar si tiene servicios asociados
        if ($plane->services()->exists()) {
            return back()->withErrors('No se puede eliminar: tiene servicios asociados.');
        }
        $plane->delete();
        return back()->with('ok','Plan eliminado.');
    }
}
