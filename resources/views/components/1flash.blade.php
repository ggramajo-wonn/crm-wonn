@if (session('ok'))
  <div class="mb-4 rounded-lg border border-emerald-800 bg-emerald-900/20 p-3 text-emerald-200">
    {{ session('ok') }}
  </div>
@endif
@if ($errors->any())
  <div class="mb-4 rounded-lg border border-red-800 bg-red-900/20 p-3 text-red-200">
    <ul class="list-disc ml-5">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif