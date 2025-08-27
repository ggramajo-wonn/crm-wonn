@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Servidor de Emails</h1>
    <a href="{{ route('panel') }}" class="text-sm text-gray-400 hover:text-primary-400">Volver</a>
  </div>

  {{-- NOTA: no mostramos flashes locales para evitar duplicados si el layout ya los muestra --}}

  <form action="{{ route('config.email.update') }}" method="post" class="space-y-6">
    @csrf

    <div class="rounded-xl border border-gray-800 p-4">
      <h2 class="mb-4 text-lg font-semibold">Tipo de autenticación</h2>
      <div class="grid md:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-400">Autenticación</span>
          <select name="auth_type" id="auth_type" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2" onchange="toggleAuthFields()">
            <option value="userpass" {{ ($settings['auth_type'] ?? '') === 'userpass' ? 'selected' : '' }}>Usuario y Contraseña</option>
            <option value="oauth_google" {{ ($settings['auth_type'] ?? '') === 'oauth_google' ? 'selected' : '' }}>OAuth2 Google Gmail</option>
          </select>
        </label>
      </div>
    </div>

    <div id="userpass_fields" class="rounded-xl border border-gray-800 p-4">
      <h2 class="mb-4 text-lg font-semibold">Datos SMTP (Usuario y Contraseña)</h2>
      <div class="grid md:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-400">Host/Servidor</span>
          <input type="text" name="host" value="{{ old('host', $settings['host'] ?? '') }}" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2" placeholder="smtp.tu-dominio.com">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Seguridad</span>
          <select name="security" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2">
            @php $sec = old('security', $settings['security'] ?? 'TLS'); @endphp
            <option value="SSL" {{ $sec === 'SSL' ? 'selected' : '' }}>SSL</option>
            <option value="TLS" {{ $sec === 'TLS' ? 'selected' : '' }}>TLS</option>
            <option value="NINGUNO" {{ $sec === 'NINGUNO' ? 'selected' : '' }}>NINGUNO</option>
          </select>
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Usuario/Correo</span>
          <input type="text" name="username" value="{{ old('username', $settings['username'] ?? '') }}" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2" placeholder="cuenta@tu-dominio.com">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Contraseña</span>
          <input type="password" name="password" value="{{ old('password', $settings['password'] ?? '') }}" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Puerto</span>
          <input type="number" name="port" value="{{ old('port', $settings['port'] ?? 465) }}" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2" min="1" max="65535">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Autenticación</span>
          @php $auth = old('auth_enabled', $settings['auth_enabled'] ?? true) ? true : false; @endphp
          <select name="auth_enabled" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2">
            <option value="1" {{ $auth ? 'selected' : '' }}>Sí</option>
            <option value="0" {{ !$auth ? 'selected' : '' }}>No</option>
          </select>
        </label>
      </div>
    </div>

    <div id="oauth_fields" class="rounded-xl border border-gray-800 p-4 hidden">
      <h2 class="mb-4 text-lg font-semibold">OAuth2 Google (Gmail)</h2>
      <p class="text-sm text-gray-400">
        Próximamente: configuración de OAuth (client id/secret, refresh token, etc.).
        Por ahora, usá "Usuario y Contraseña" para el envío SMTP tradicional.
      </p>
    </div>

    <div class="rounded-xl border border-gray-800 p-4">
      <h2 class="mb-4 text-lg font-semibold">Remitente y límites</h2>
      <div class="grid md:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-400">Remitente (From) — Dirección</span>
          <input type="email" name="from_address" value="{{ old('from_address', $settings['from_address'] ?? '') }}" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2" placeholder="noreply@tu-dominio.com">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Remitente (From) — Nombre</span>
          <input type="text" name="from_name" value="{{ old('from_name', $settings['from_name'] ?? ($company->name ?? '')) }}" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2">
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Límite de envío</span>
          @php $sendLimit = old('send_limit', $settings['send_limit'] ?? 'none'); @endphp
          <select name="send_limit" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2">
            <option value="none" {{ $sendLimit === 'none' ? 'selected' : '' }}>Sin límite</option>
            <option value="per_day" {{ $sendLimit === 'per_day' ? 'selected' : '' }}>Límite por día</option>
            <option value="per_hour" {{ $sendLimit === 'per_hour' ? 'selected' : '' }}>Límite por hora</option>
          </select>
        </label>

        <label class="block">
          <span class="text-sm text-gray-400">Límite de correos</span>
          <input type="number" name="limit_count" value="{{ old('limit_count', $settings['limit_count'] ?? '') }}" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2" min="1">
        </label>
      </div>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-white hover:bg-primary-500">Guardar cambios</button>
      <a href="{{ route('config.email') }}" class="rounded-lg border border-gray-700 px-4 py-2 text-gray-300 hover:bg-gray-800">Descartar</a>
    </div>
  </form>

  <div class="mt-10 rounded-xl border border-gray-800 p-4">
    <h2 class="mb-4 text-lg font-semibold">Prueba rápida de envío</h2>
    <form action="{{ route('config.email.test') }}" method="post" class="grid md:grid-cols-2 gap-4">
      @csrf
      <label class="block md:col-span-2">
        <span class="text-sm text-gray-400">Para</span>
        <input type="email" name="to" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2" placeholder="correo@destinatario.com" required>
      </label>

      <label class="block md:col-span-2">
        <span class="text-sm text-gray-400">Asunto</span>
        <input type="text" name="subject" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2" placeholder="Prueba de correo">
      </label>

      <label class="block md:col-span-2">
        <span class="text-sm text-gray-400">Mensaje</span>
        <textarea name="message" rows="4" class="mt-1 w-full rounded-md border border-gray-700 bg-gray-900 p-2" placeholder="Texto del mensaje (opcional)"></textarea>
      </label>

      <div class="md:col-span-2">
        <button class="rounded-lg bg-primary-600 px-4 py-2 text-white hover:bg-primary-500">Enviar prueba</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleAuthFields() {
  var type = document.getElementById('auth_type').value;
  var up = document.getElementById('userpass_fields');
  var oa = document.getElementById('oauth_fields');
  if (type === 'oauth_google') {
    up.classList.add('hidden');
    oa.classList.remove('hidden');
  } else {
    oa.classList.add('hidden');
    up.classList.remove('hidden');
  }
}
toggleAuthFields();
</script>
@endsection
