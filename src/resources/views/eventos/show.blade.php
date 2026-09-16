@php
    $miInscripcion = auth()->check()
        ? auth()->user()->inscripciones->firstWhere('id', $evento->id)?->pivot
        : null;

    $plazasLibres = $evento->cuposDisponibles();
@endphp

<x-layout-publico :titulo="$evento->titulo">
    <x-slot name="header">
        <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">
            {{ $evento->categoria->nombre }}
        </p>
        <h1 class="mt-1 text-2xl font-semibold">{{ $evento->titulo }}</h1>
    </x-slot>

    <div class="grid gap-8 lg:grid-cols-3">

        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <h2 class="font-semibold mb-3">{{ __('Description') }}</h2>
            <p class="text-sm leading-relaxed whitespace-pre-line text-gray-700 dark:text-gray-300">
                {{ $evento->descripcion }}
            </p>

            <p class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Organized by :name', ['name' => $evento->organizador->name]) }}
            </p>
        </div>

        <aside class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 space-y-4 h-fit">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Starts') }}</dt>
                    <dd class="font-medium">{{ $evento->inicia_el->format('d/m/Y H:i') }}</dd>
                </div>
                @if ($evento->termina_el)
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Ends') }}</dt>
                        <dd class="font-medium">{{ $evento->termina_el->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Location') }}</dt>
                    <dd class="font-medium">{{ $evento->lugar }} ({{ $evento->modalidad }})</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Price') }}</dt>
                    <dd class="font-medium">{{ $evento->es_gratuito ? __('Free') : 'Bs '.$evento->precio }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Spots') }}</dt>
                    <dd class="font-medium">{{ __(':available of :total', ['available' => max($plazasLibres, 0), 'total' => $evento->cupo]) }}</dd>
                </div>
            </dl>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                @guest
                    <a href="{{ route('login') }}"
                       class="block text-center px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                        {{ __('Log in to register') }}
                    </a>
                @endguest

                @auth
                    @if ($miInscripcion && $miInscripcion->estado !== 'cancelada')
                        <p class="text-sm mb-3">
                            {{ __('You are registered') }} · <x-estado-badge :estado="$miInscripcion->estado" />
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                            {{ __('Code') }}: <span class="font-mono">{{ $miInscripcion->codigo }}</span>
                        </p>

                        {{-- Ruta 26: cancelar es PATCH, no DELETE. --}}
                        <form method="POST" action="{{ route('inscripciones.cancelar', $evento) }}">
                            @csrf
                            @method('PATCH')
                            <button class="w-full px-4 py-2 rounded-md border border-red-300 text-red-700 dark:text-red-300 dark:border-red-700 hover:bg-red-50 dark:hover:bg-red-900/30">
                                {{ __('Cancel Registration') }}
                            </button>
                        </form>
                    @else
                        {{-- Ruta 25: el middleware inscripcion.abierta valida estado, fecha y cupo. --}}
                        <form method="POST" action="{{ route('inscripciones.store', $evento) }}">
                            @csrf
                            <button class="w-full px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                                {{ __('Register for Event') }}
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </aside>
    </div>
</x-layout-publico>
