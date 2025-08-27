@extends('layouts.app')
@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Planes</h1>
    <a href="{{ route('planes.create') }}" class="rounded-lg bg-primary-600 hover:bg-primary-500 px-4 py-2">Nuevo</a>
  </div>

  <x-flash />

  <div class="overflow-x-auto rounded-xl border border-gray-800">
    <table class="min-w-full text-left">
      <thead class="bg-gray-900 text-gray-400">
        <tr>
          <th class="p-3">Nombre</th>
          <th class="p-3">Precio</th>
          <th class="p-3">Clientes activos</th>
          <th class="p-3">Clientes suspendidos</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $p)
          <tr class="border-t border-gray-800">
            <td class="p-3">
              <div class="font-medium">{{ $p->name }}</div>
              @if($p->mb_down || $p->mb_up || $p->description)
                <div class="text-xs text-gray-400">
                  @if($p->mb_down) ↓ {{ $p->mb_down }}Mb @endif
                  @if($p->mb_up)  ↑ {{ $p->mb_up }}Mb @endif
                  @if($p->description) — {{ $p->description }} @endif
                </div>
              @endif
            </td>
            <td class="p-3">$ {{ number_format($p->price,2,',','.') }}</td>
            <td class="p-3">{{ $p->activos_count }}</td>
            <td class="p-3">{{ $p->suspendidos_count }}</td>
            <td class="p-3 text-right">
              <a href="{{ route('planes.edit', $p) }}" class="text-primary-400 hover:underline">Gestionar</a>
              <form action="{{ route('planes.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este plan?');">
                @csrf @method('DELETE')
                <button class="ml-3 text-red-400 hover:underline">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td class="p-4 text-gray-400" colspan="5">Sin planes aún.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $items->links() }}</div>
@endsection
