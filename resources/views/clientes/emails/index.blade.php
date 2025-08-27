@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold">Emails a clientes</h1>
  @if (Route::has('clientes.emails.create'))
  <a href="{{ route('clientes.emails.create') }}"
     class="inline-flex items-center gap-2 bg-[#0EA5B7] hover:opacity-90 text-white font-medium px-4 py-2 rounded-lg">
     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h5V6a1 1 0 011-1z"/></svg>
     Nuevo envío
  </a>
  @endif
</div>

{{-- Filtros compactos --}}
<form method="GET" action="{{ route('clientes.emails.index') }}" class="mb-4">
  <div class="flex flex-nowrap items-end gap-3 overflow-x-auto pb-1">
    <div class="min-w-[340px]">
      <label class="block text-sm text-gray-300 mb-1">Filtrar</label>
      <input type="text" name="q" value="{{ request('q') }}"
             class="w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2 placeholder-gray-400"
             placeholder="Nombre, ID, email o asunto">
    </div>
    <div class="min-w-[160px]">
      <label class="block text-sm text-gray-300 mb-1">Desde</label>
      <input type="date" name="fromDate" value="{{ request('fromDate') }}"
             class="w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2">
    </div>
    <div class="min-w-[160px]">
      <label class="block text-sm text-gray-300 mb-1">Hasta</label>
      <input type="date" name="toDate" value="{{ request('toDate') }}"
             class="w-full bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-3 py-2">
    </div>
    <div class="flex items-end gap-2">
      <button class="bg-[#0EA5B7] hover:opacity-90 text-white font-medium px-4 py-2 rounded-lg" type="submit">Filtrar</button>
      <a href="{{ route('clientes.emails.index') }}" class="border border-gray-600 text-gray-100 px-4 py-2 rounded-lg">Limpiar</a>
    </div>
    <div class="ml-auto flex items-end gap-2">
      <label class="text-sm text-gray-300">Mostrar</label>
      <select name="per_page" onchange="this.form.submit()"
              class="bg-gray-800 text-gray-100 border border-gray-700 rounded-lg px-2 py-2">
        @php $pp = request('per_page', 25); @endphp
        @foreach ([10,25,50,100,500,'all'] as $opt)
          <option value="{{ $opt }}" @selected((string)$pp === (string)$opt)>
            {{ $opt === 'all' ? 'todas' : $opt }}
          </option>
        @endforeach
      </select>
    </div>
  </div>
</form>

{{-- Tabla --}}
<div class="rounded-2xl border border-gray-800 overflow-hidden">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-900/50">
      <tr class="text-gray-300">
        <th class="text-left px-4 py-3">Fecha</th>
        <th class="text-left px-4 py-3">Destinatario</th>
        <th class="text-left px-4 py-3">Asunto</th>
        <th class="text-left px-4 py-3">Estado</th>
        <th class="text-left px-4 py-3">Vista previa</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-800">
      @forelse ($logs as $log)
        <tr class="text-gray-200">
          <td class="px-4 py-3">{{ optional($log->created_at)->format('d/m/Y H:i') }}</td>
          <td class="px-4 py-3">
            <a href="mailto:{{ $log->to }}" class="text-cyan-400 hover:underline">{{ $log->to }}</a>
            @if(!empty($log->client_id))
              <div class="text-xs text-gray-400">
                #{{ $log->client_id }}
                @if(optional($log->client)->name) — {{ $log->client->name }} @endif
              </div>
            @endif
          </td>
          <td class="px-4 py-3">{{ $log->subject }}</td>
          <td class="px-4 py-3">
            @php
              $status = strtolower((string) $log->status);
              $badge  = 'bg-gray-700 text-gray-100';
              if ($status === 'sent')   $badge = 'bg-emerald-900/60 text-emerald-200';
              if ($status === 'failed') $badge = 'bg-red-900/60 text-red-200';
              if ($status === 'queued') $badge = 'bg-sky-900/60 text-sky-200';
            @endphp
            <span class="px-2 py-1 rounded {{ $badge }}">{{ $status }}</span>
          </td>
          <td class="px-4 py-3">
            <button type="button"
                    class="btn-preview border border-gray-600 hover:border-gray-500 text-gray-100 px-3 py-1 rounded-lg"
                    data-id="{{ $log->id }}"
                    data-to="{{ $log->to }}"
                    data-subject="{{ $log->subject }}"
                    data-body='@json($log->body)'
                    data-created="{{ optional($log->created_at)->format('d/m/Y H:i') }}"
                    data-from="{{ $log->from ?? ($defaultFrom ?? config('mail.from.address')) }}">
              VER
            </button>
          </td>
        </tr>
      @empty
        <tr><td class="px-4 py-6 text-gray-400" colspan="5">Sin registros para los filtros aplicados.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Paginación --}}
@if ($logs instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-4">{{ $logs->links() }}</div>
@endif

{{-- Modal de vista previa --}}
<div id="previewModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/60"></div>
  <div class="relative mx-auto max-w-3xl w-[92%] mt-10 bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl">
    <div class="flex items-center justify-between p-4 border-b border-gray-800">
      <h3 class="text-lg font-semibold text-gray-100">Vista previa</h3>
      <button id="modalClose" class="px-3 py-1 rounded-lg border border-gray-600 text-gray-100 hover:border-gray-500">Cerrar</button>
    </div>
    <div class="p-4 space-y-2 text-gray-200">
      <div><span class="text-gray-400">De:</span> <span id="pvFrom"></span></div>
      <div><span class="text-gray-400">Para:</span> <span id="pvTo"></span></div>
      <div><span class="text-gray-400">Fecha:</span> <span id="pvDate"></span></div>
      <div><span class="text-gray-400">Asunto:</span> <span id="pvSubj"></span></div>
      <div class="mt-3 p-3 bg-gray-800 rounded-lg border border-gray-700">
        <div id="pvBody" class="prose prose-invert max-w-none"></div>
      </div>
    </div>
  </div>
</div>

{{-- Script inline para el modal --}}
<script>
(function() {
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-preview');
    if (!btn) return;

    const m = document.getElementById('previewModal');
    document.getElementById('pvFrom').textContent = btn.dataset.from || '';
    document.getElementById('pvTo').textContent   = btn.dataset.to || '';
    document.getElementById('pvDate').textContent = btn.dataset.created || '';
    document.getElementById('pvSubj').textContent = btn.dataset.subject || '';

    try {
      let body = JSON.parse(btn.dataset.body || '""');
      if (!/<[a-z][\\s\\S]*>/i.test(body)) { body = body.replace(/\\n/g, '<br>'); }
      document.getElementById('pvBody').innerHTML = body;
    } catch (err) {
      document.getElementById('pvBody').textContent = '(No se pudo cargar el contenido)';
    }

    m.classList.remove('hidden');
  });

  document.getElementById('modalClose').addEventListener('click', function() {
    document.getElementById('previewModal').classList.add('hidden');
  });

  document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target.id === 'previewModal') e.currentTarget.classList.add('hidden');
  });
})();
</script>

@endsection
