<?php
namespace App\Http\Controllers;

use App\Models\{Company, MikrotikRouter};
use Illuminate\Http\Request;

class MikrotikRouterController extends Controller
{
    public function create()
    {
        $router = new MikrotikRouter();
        return view('routers.create', compact('router'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'ip'         => 'required|ip',              // por ahora solo IP v4 válida
            'api_user'   => 'required|string|max:100',
            'api_password'=> 'required|string|max:255',
        ]);

        $data['company_id'] = Company::first()?->id;
        MikrotikRouter::create($data);

        return redirect()->route('empresa.edit')->with('ok', 'Router agregado.');
    }

    public function edit(MikrotikRouter $router)
    {
        return view('routers.edit', compact('router'));
    }

    public function update(Request $request, MikrotikRouter $router)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'ip'         => 'required|ip',
            'api_user'   => 'required|string|max:100',
            'api_password'=> 'nullable|string|max:255', // si viene vacío, no se cambia
        ]);

        if ($request->filled('api_password')) {
            $router->update($data);
        } else {
            unset($data['api_password']);
            $router->update($data);
        }

        return redirect()->route('empresa.edit')->with('ok', 'Router actualizado.');
    }

    public function destroy(MikrotikRouter $router)
    {
        $router->delete();
        return back()->with('ok', 'Router eliminado.');
    }
}
