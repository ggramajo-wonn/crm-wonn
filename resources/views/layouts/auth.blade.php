<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'WONN') }} — Acceso</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: {50:'#ecfeff',100:'#cffafe',200:'#a5f3fc',300:'#67e8f9',400:'#22d3ee',500:'#06b6d4',600:'#0891b2',700:'#0e7490',800:'#155e75',900:'#164e63'}
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-950 text-gray-200 min-h-screen">
  <main class="min-h-screen grid place-items-center p-4">
    <div class="w-full max-w-md">
      @yield('content')
    </div>
  </main>
</body>
</html>
