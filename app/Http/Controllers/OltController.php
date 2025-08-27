<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use App\Models\Nap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OltController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $olts = Olt::query()
            ->when($q !== '', function($w) use ($q){
                $like = '%' . $q . '%';
                $w->where('name','like',$like)->orWhere('localidad','like',$like);
            })
            ->withCount('naps')
            ->orderBy('id', 'asc')
            ->get();

        return view('olts.index', compact('olts', 'q'));
    }

    public function create()
    {
        $olt = new Olt();
        return view('olts.create', compact('olt'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'localidad' => ['nullable','string','max:255'],
        ]);
        $olt = Olt::create($data);
        return redirect()->route('olts.show', $olt->id)->with('ok','OLT creada.');
    }

    public function show($id)
    {
        $olt = Olt::findOrFail($id);
        $naps = Nap::where('olt_id', $olt->id)->orderBy('id','asc')->get();
        $usage = $this->loadNapUsage($naps);
        return view('olts.show', compact('olt','naps','usage'));
    }

    public function edit($id)
    {
        $olt = Olt::findOrFail($id);
        return view('olts.edit', compact('olt'));
    }

    public function update(Request $request, $id)
    {
        $olt = Olt::findOrFail($id);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'localidad' => ['nullable','string','max:255'],
        ]);
        $olt->update($data);
        return redirect()->route('olts.show', $olt->id)->with('ok','OLT actualizada.');
    }

    public function destroy($id)
    {
        $olt = Olt::findOrFail($id);
        if ($olt->naps()->count() > 0) {
            return redirect()->back()->with('error','No se puede eliminar: la OLT tiene NAPs asociadas.');
        }
        $olt->delete();
        return redirect()->route('olts.index')->with('ok','OLT eliminada.');
    }

    private function loadNapUsage($naps)
    {
        $usage = [];
        $hasNapId   = Schema::hasColumn('services','nap_id');
        $hasNapCol  = Schema::hasColumn('services','nap');
        $hasPortCol = Schema::hasColumn('services','nap_port') || Schema::hasColumn('services','puerto') || Schema::hasColumn('services','port');

        if (!$hasPortCol) {
            foreach ($naps as $nap) $usage[$nap->id] = [];
            return $usage;
        }

        $rows = DB::table('services')
            ->select(['id','client_id','ip','router','router_id','nap_id','nap','nap_port','puerto','port','gps'])
            ->get();

        foreach ($naps as $nap) {
            $napUsage = [];
            for ($i=1; $i<= (int)$nap->puertos; $i++) {
                $napUsage[$i] = ['used'=>false,'label'=>''];
            }
            foreach ($rows as $row) {
                $port = (int)($row->nap_port ?? $row->puerto ?? $row->port ?? 0);
                if ($port < 1 || $port > (int)$nap->puertos) continue;

                $match = false;
                if ($hasNapId && (int)$row->nap_id === (int)$nap->id) $match = True;
                if (!$match && $hasNapCol && (string)$row->nap === (string)$nap->id) $match = True;
                if (!$match && $hasNapCol && (string)$row->nap === (string)$nap->name) $match = True;
                if (!$match) continue;

                $label = 'Servicio #' . $row->id;
                try {
                    if (Schema::hasColumn('clients','name') && Schema::hasColumn('services','client_id')) {
                        $client = DB::table('clients')->where('id', $row->client_id)->first();
                        if ($client and isset($client->name)) $label = $client->name . ' (Srv ' . $row->id . ')';
                    }
                } catch (\Throwable $e) {}
                $napUsage[$port] = ['used'=>true,'label'=>$label];
            }
            $usage[$nap->id] = $napUsage;
        }
        return $usage;
    }
}
