<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Mostrar formulario de edición de la empresa.
     */
    public function edit()
    {
        // Obtenemos el único registro (o instanciamos uno nuevo para evitar errores en la vista)
        $company = Company::first() ?? new Company([
            'name'           => null,
            'cuit'           => null,
            'fantasy_name'   => null,
            'address'        => null,
            'locality'       => null,
            'postal_code'    => null,
            'phones'         => null,
            'website'        => null,
            'google_maps_key'=> null,
            'logo_path'      => null,
        ]);

        return view('empresa.edit', compact('company'));
    }

    /**
     * Actualiza datos de la empresa.
     * Soporta remover logo (remove_logo=1), subir logo y guardar nuevos campos.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required','string','max:255'],  // Razón social
            'cuit'             => ['nullable','regex:/^\d{2}-?\d{8}-?\d$/'],
            'fantasy_name'     => ['nullable','string','max:255'],
            'address'          => ['nullable','string','max:255'],
            'locality'         => ['nullable','string','max:255'],
            'postal_code'      => ['nullable','string','max:20'],
            'phones'           => ['nullable','string','max:255'],
            'website'          => ['nullable','string','max:255'],
            'google_maps_key'  => ['nullable','string','max:255'],

            'logo'             => ['nullable','image','mimes:jpg,jpeg,png,webp,svg','max:5120'],
            'remove_logo'      => ['nullable','in:1'],
        ],[
            'cuit.regex' => 'Formato de CUIT inválido. Ej: 20-12345678-3',
            'logo.image' => 'El archivo debe ser una imagen (jpg, jpeg, png, webp o svg).',
        ]);

        $company = Company::first() ?? new Company();

        // Quitar logo si se solicitó
        if ($request->boolean('remove_logo')) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $company->logo_path = null;
        }

        // Subir nuevo logo
        if ($request->hasFile('logo')) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $company->logo_path = $request->file('logo')->store('logos','public');
        }

        // Asignar campos
        $company->name            = $data['name'];
        $company->cuit            = $data['cuit'] ?? null;
        $company->fantasy_name    = $data['fantasy_name'] ?? null;
        $company->address         = $data['address'] ?? null;
        $company->locality        = $data['locality'] ?? null;
        $company->postal_code     = $data['postal_code'] ?? null;
        $company->phones          = $data['phones'] ?? null;
        $company->website         = $data['website'] ?? null;
        $company->google_maps_key = $data['google_maps_key'] ?? null;

        $company->save();

        return back()->with('ok', 'Cambios guardados.');
    }

    /**
     * Endpoint opcional para eliminar logo (no se usa si el botón envía remove_logo=1).
     */
    public function destroyLogo()
    {
        $company = Company::firstOrFail();

        if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
            Storage::disk('public')->delete($company->logo_path);
        }

        $company->update(['logo_path' => null]);

        return back()->with('ok', 'Logo eliminado.');
    }
}
