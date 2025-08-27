@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Nuevo envío de emails</h1>

@php
    $clientsForJs = collect($clients ?? [])->map(function ($c) {
        return ['id' => $c->id, 'name' => $c->name, 'email' => $c->email];
    })->values();
@endphp

@if ($errors->any())
  <div class="mb-4 rounded-lg border border-red-800 bg-red-900/30 text-red-200 px-4 py-3">
    <ul class="list-disc list-inside text-sm">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('clientes.emails.store') }}" class="space-y-6">
    @csrf

    <div class="border border-gray-700 rounded-2xl p-5 bg-gray-900/40">
        <h2 class="text-lg font-semibold mb-4">Destinatarios</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1">
                <label for="audience" class="block text-sm text-gray-300">Audiencia</label>
                <select id="audience" name="audience"
                        class="w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-600">
                    <option value="all" @selected(old('audience','all')==='all')>Todos los clientes</option>
                    <option value="by_name" @selected(old('audience')==='by_name')>Por nombre</option>
                </select>
            </div>
        </div>

        <div id="byNameBlock" class="mt-4 hidden">
            <div class="space-y-2">
                <label class="block text-sm text-gray-300">Buscar cliente</label>
                <div class="relative">
                    <input id="clientSearch" type="text"
                           placeholder="Escribí nombre o email..."
                           class="w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-600"
                           autocomplete="off">
                    <div id="clientSuggest"
                         class="absolute z-10 mt-1 w-full bg-gray-900 border border-gray-700 rounded-lg shadow-lg max-h-64 overflow-auto hidden"></div>
                </div>
                <div id="selectedClients" class="mt-2 flex flex-wrap gap-2"></div>
            </div>
        </div>
    </div>

    <div class="border border-gray-700 rounded-2xl p-5 bg-gray-900/40">
        <h2 class="text-lg font-semibold mb-4">Contenido</h2>

        <div class="space-y-4">
            <div>
                <label for="subject" class="block text-sm text-gray-300">Asunto</label>
                <input id="subject" name="subject" type="text" required
                       value="{{ old('subject') }}"
                       class="w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-600">
            </div>

            <div>
                <label for="body" class="block text-sm text-gray-300">Mensaje</label>
                <textarea id="body" name="body" rows="8" required
                          class="w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-600">{{ old('body') }}</textarea>
                <p class="mt-2 text-xs text-gray-400">
                    Placeholders disponibles:
                    <span class="px-2 py-0.5 rounded bg-gray-800 border border-gray-700 text-gray-200">{name}</span>
                    <span class="px-2 py-0.5 rounded bg-gray-800 border border-gray-700 text-gray-200">{dni}</span>
                    <span class="px-2 py-0.5 rounded bg-gray-800 border border-gray-700 text-gray-200">{localidad}</span>
                    <span class="px-2 py-0.5 rounded bg-gray-800 border border-gray-700 text-gray-200">{email}</span>
                    <span class="px-2 py-0.5 rounded bg-gray-800 border border-gray-700 text-gray-200">{saldo}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="bg-[#0EA5B7] hover:opacity-90 text-white font-medium px-4 py-2 rounded-lg">Enviar</button>
        <a href="{{ route('clientes.emails.index') }}" class="border border-gray-600 text-gray-100 px-4 py-2 rounded-lg">Cancelar</a>
    </div>
</form>

<script>
(function() {
    const audienceSel  = document.getElementById('audience');
    const byNameBlock  = document.getElementById('byNameBlock');
    const search       = document.getElementById('clientSearch');
    const suggest      = document.getElementById('clientSuggest');
    const selectedWrap = document.getElementById('selectedClients');

    window.CLIENTS = window.CLIENTS || @json($clientsForJs);
    const selectedIds = new Set();

    function refreshMode() {
        const mode = audienceSel.value;
        if (mode === 'by_name') {
            byNameBlock.classList.remove('hidden');
        } else {
            byNameBlock.classList.add('hidden');
        }
    }

    function clearSuggest() { suggest.innerHTML=''; suggest.classList.add('hidden'); }
    function renderSuggest(items) {
        if (!items.length) { clearSuggest(); return; }
        suggest.innerHTML = items.map(function(c){
            return '<button type="button" data-id="'+c.id+'" class="w-full text-left px-3 py-2 hover:bg-gray-800 focus:bg-gray-800">'+
                     '<div class="text-gray-100">'+(c.name||'')+'</div>'+
                     '<div class="text-xs text-gray-400">'+(c.email||'')+'</div>'+
                   '</button>';
        }).join('');
        suggest.classList.remove('hidden');
    }
    function addChip(c) {
        const key = String(c.id); if (selectedIds.has(key)) return;
        selectedIds.add(key);
        const chip = document.createElement('div');
        chip.className = 'relative inline-flex items-center gap-2 pl-3 pr-7 py-1 rounded-full bg-gray-800 border border-gray-700 text-gray-100';
        chip.innerHTML = ''+
            '<input type="hidden" name="client_ids[]" value="'+c.id+'">'+
            '<span>'+(c.name||'')+(c.email ? ' ('+c.email+')' : '')+'</span>'+
            '<button type="button" aria-label="Quitar" class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-gray-700 hover:bg-gray-600 text-gray-200 flex items-center justify-center text-xs remove-chip">&times;</button>';
        chip.querySelector('.remove-chip').addEventListener('click', function(){ selectedIds.delete(key); chip.remove(); });
        selectedWrap.appendChild(chip);
    }
    search && search.addEventListener('input', function(e){
        const q = (e.target.value || '').trim().toLowerCase();
        if (!q) { clearSuggest(); return; }
        const matches = (window.CLIENTS||[]).filter(function(c){
            return (c.name && c.name.toLowerCase().includes(q)) ||
                   (c.email && c.email.toLowerCase().includes(q));
        }).slice(0,10);
        renderSuggest(matches);
    });
    suggest && suggest.addEventListener('click', function(e){
        const btn = e.target.closest('button[data-id]'); if(!btn) return;
        const id = btn.getAttribute('data-id');
        const c = (window.CLIENTS||[]).find(function(x){ return String(x.id) === String(id); });
        if (c) { addChip(c); search.value=''; clearSuggest(); search.focus(); }
    });
    document.addEventListener('click', function(e){ if (!suggest.contains(e.target) && e.target !== search) clearSuggest(); });

    refreshMode();
    audienceSel && audienceSel.addEventListener('change', refreshMode);
})();
</script>
@endsection
