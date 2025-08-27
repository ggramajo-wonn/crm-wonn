<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use App\Models\Nap;
use Illuminate\Http\Request;

class NapController extends Controller
{
    public function create($oltId)
    {
        $olt = Olt::findOrFail($oltId);
        $nap = new Nap(['olt_id'=>$olt->id, 'puertos'=>8]);
        return view('naps.create', compact('olt','nap'));
    }

    public function store(Request $request, $oltId)
    {
        $olt = Olt::findOrFail($oltId);
        $data = $request->validate([
            'name'      => ['required','string','max:255'],
            'ubicacion' => ['nullable','string','max:255'],
            'gps'       => ['nullable','string','max:255'],
            'puertos'   => ['required','integer','min:1','max:128'],
            'detalles'  => ['nullable','string'],
        ]);
        $data['olt_id'] = $olt->id;
        Nap::create($data);
        return redirect()->route('olts.show', $olt->id)->with('ok','NAP creada.');
    }

    public function edit($oltId, $id)
    {
        $olt = Olt::findOrFail($oltId);
        $nap = Nap::where('olt_id',$olt->id)->findOrFail($id);
        return view('naps.edit', compact('olt','nap'));
    }

    public function update(Request $request, $oltId, $id)
    {
        $olt = Olt::findOrFail($oltId);
        $nap = Nap::where('olt_id',$olt->id)->findOrFail($id);
        $data = $request->validate([
            'name'      => ['required','string','max:255'],
            'ubicacion' => ['nullable','string','max:255'],
            'gps'       => ['nullable','string','max:255'],
            'puertos'   => ['required','integer','min:1','max:128'],
            'detalles'  => ['nullable','string'],
        ]);
        $nap->update($data);
        return redirect()->route('olts.show', $olt->id)->with('ok','NAP actualizada.');
    }

    public function destroy($oltId, $id)
    {
        $olt = Olt::findOrFail($oltId);
        $nap = Nap::where('olt_id',$olt->id)->findOrFail($id);
        $nap->delete();
        return redirect()->route('olts.show', $olt->id)->with('ok','NAP eliminada.');
    }
}
