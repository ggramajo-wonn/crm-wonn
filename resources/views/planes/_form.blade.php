@csrf
<div class="grid sm:grid-cols-2 gap-4">
  <label class="block">
    <span class="text-sm text-gray-400">Nombre del plan</span>
    <input type="text" name="name" value="{{ old('name', $plan->name) }}" required
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>
  <label class="block">
    <span class="text-sm text-gray-400">Precio</span>
    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $plan->price) }}" required
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>
  <label class="block">
    <span class="text-sm text-gray-400">MB Down</span>
    <input type="number" min="0" name="mb_down" value="{{ old('mb_down', $plan->mb_down) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>
  <label class="block">
    <span class="text-sm text-gray-400">MB Up</span>
    <input type="number" min="0" name="mb_up" value="{{ old('mb_up', $plan->mb_up) }}"
           class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">
  </label>
  <label class="block sm:col-span-2">
    <span class="text-sm text-gray-400">Descripción</span>
    <textarea name="description" rows="3" class="mt-1 w-full rounded-lg bg-gray-900 border border-gray-700 px-3 py-2">{{ old('description', $plan->description) }}</textarea>
  </label>
</div>
<div class="mt-4 flex gap-3">
  <button class="rounded-lg bg-primary-600 hover:bg-primary-500 px-4 py-2">Guardar</button>
  <a href="{{ route('planes.index') }}" class="px-4 py-2 rounded-lg border border-gray-700 hover:bg-gray-900">Cancelar</a>
</div>
