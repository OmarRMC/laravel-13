@props(['titulo' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $titulo ? $titulo.' · '.config('app.name') : config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="font-semibold text-lg">
                {{ config('app.name') }}
            </a>

            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('eventos.index') }}"
                   class="{{ request()->routeIs('eventos.*') ? 'font-semibold text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                    Eventos
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Mi panel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                        Registrarse
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-flash />

        {{ $slot }}
    </main>

    <footer class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-sm text-gray-500 dark:text-gray-400">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </footer>
</body>
</html>
