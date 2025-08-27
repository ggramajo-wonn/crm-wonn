@php
  $isFtth  = old('ftth', isset($service) ? (bool)($service->ftth ?? false) : false);
  $selNap  = old('nap_id', isset($service) ? ($service->nap_id ?? null) : null);
  $selPort = old('nap_port', isset($service) ? ($service->nap_port ?? null) : null);
@endphp

<div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-2">
  <div class="md:col-span-2 flex items-center gap-2">
    <input type="checkbox" name="ftth" id="ftth" value="1"
           @checked($isFtth)
           class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-emerald-500 focus:ring-emerald-500">
    <label for="ftth" class="text-slate-200 font-medium">FTTH</label>
  </div>

  <div id="ftth-nap-wrap" class="md:col-span-5 {{ $isFtth ? '' : 'hidden' }}">
    <label class="block text-slate-400 text-sm mb-1">NAP</label>
    <select name="nap_id" id="nap_id"
            class="w-full rounded-md bg-slate-800 border border-slate-700 text-slate-200 px-3 py-2">
      <option value="">Seleccionar NAP…</option>
      @foreach(($naps ?? []) as $nap)
        <option value="{{ $nap->id }}" data-ports="{{ $nap->puertos ?? 0 }}" @selected($selNap == $nap->id)>
          #{{ $nap->id }} — {{ $nap->name }} @if(!empty($nap->puertos)) ({{ $nap->puertos }} puertos) @endif
        </option>
      @endforeach
    </select>
  </div>

  <div id="ftth-port-wrap" class="md:col-span-5 {{ $isFtth ? '' : 'hidden' }}">
    <label class="block text-slate-400 text-sm mb-1">Puerto</label>
    <input type="number" name="nap_port" id="nap_port" min="1" value="{{ $selPort }}" placeholder="N° de puerto"
      class="w-full rounded-md bg-slate-800 border border-slate-700 text-slate-200 px-3 py-2">
    <p class="text-xs text-slate-400 mt-1">Rango sugerido: <span id="nap_port_help">—</span></p>
  </div>
</div>

{{-- Script inline (no depende de @stack('scripts')) --}}
<script>
(function(){
  function ready(fn){ if(document.readyState !== 'loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }
  ready(function(){
    const ftth     = document.getElementById('ftth');
    const napWrap  = document.getElementById('ftth-nap-wrap');
    const portWrap = document.getElementById('ftth-port-wrap');
    const napSel   = document.getElementById('nap_id');
    const port     = document.getElementById('nap_port');
    const help     = document.getElementById('nap_port_help');

    function refresh(){
      const on = !!(ftth && ftth.checked);
      if (napWrap)  napWrap.classList.toggle('hidden', !on);
      if (portWrap) portWrap.classList.toggle('hidden', !on);
      if (!on) return;

      const opt = napSel && napSel.options ? napSel.options[napSel.selectedIndex] : null;
      const max = opt && opt.dataset && opt.dataset.ports ? parseInt(opt.dataset.ports, 10) : 0;
      if (port){
        if (Number.isFinite(max) && max > 0){
          port.max = max;
          if (help) help.textContent = '1..' + max;
        } else {
          port.removeAttribute('max');
          if (help) help.textContent = 'número de puerto';
        }
      }
    }

    if (ftth) ftth.addEventListener('change', refresh);
    if (napSel) napSel.addEventListener('change', refresh);
    refresh();
  });
})();
</script>
