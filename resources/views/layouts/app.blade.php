<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WONN') }}</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-100">
    <div class="min-h-screen">
        @includeIf('layouts.navigation')

        @hasSection('header')
            <header class="bg-gray-900 border-b border-gray-800">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
    </div>
</body>
</html>
