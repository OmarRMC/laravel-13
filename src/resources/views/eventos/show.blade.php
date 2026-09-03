@php
    $miInscripcion = auth()->check()
        ? auth()->user()->inscripciones->firstWhere('id', $evento->id)?->pivot
        : null;

    $plazasLibres = $evento->cupo - $evento->inscritos_count;
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
            <h2 class="font-semibold mb-3">Descripción</h2>
            <p class="text-sm leading-relaxed whitespace-pre-line text-gray-700 dark:text-gray-300">
                {{ $evento->descripcion }}
            </p>

            <p class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                Organiza {{ $evento->organizador->name }}
            </p>
        </div>

        <aside class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 space-y-4 h-fit">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Empieza</dt>
                    <dd class="font-medium">{{ $evento->inicia_el->format('d/m/Y H:i') }}</dd>
                </div>
                @if ($evento->termina_el)
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Termina</dt>
                        <dd class="font-medium">{{ $evento->termina_el->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Lugar</dt>
                    <dd class="font-medium">{{ $evento->lugar }} ({{ $evento->modalidad }})</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Precio</dt>
                    <dd class="font-medium">{{ $evento->es_gratuito ? 'Gratuito' : 'Bs '.$evento->precio }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Plazas</dt>
                    <dd class="font-medium">{{ max($plazasLibres, 0) }} de {{ $evento->cupo }}</dd>
                </div>
            </dl>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                @guest
                    <a href="{{ route('login') }}"
                       class="block text-center px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                        Entra para inscribirte
                    </a>
                @endguest

                @auth
                    @if ($miInscripcion && $miInscripcion->estado !== 'cancelada')
                        <p class="text-sm mb-3">
                            Estás inscrito · <x-estado-badge :estado="$miInscripcion->estado" />
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                            Código: <span class="font-mono">{{ $miInscripcion->codigo }}</span>
                        </p>

                        {{-- Ruta 26: cancelar es PATCH, no DELETE. --}}
                        <form method="POST" action="{{ route('inscripciones.cancelar', $evento) }}">
                            @csrf
                            @method('PATCH')
                            <button class="w-full px-4 py-2 rounded-md border border-red-300 text-red-700 dark:text-red-300 dark:border-red-700 hover:bg-red-50 dark:hover:bg-red-900/30">
                                Cancelar inscripción
                            </button>
                        </form>
                    @else
                        {{-- Ruta 25: el middleware inscripcion.abierta valida estado, fecha y cupo. --}}
                        <form method="POST" action="{{ route('inscripciones.store', $evento) }}">
                            @csrf
                            <button class="w-full px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                                Inscribirme
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </aside>
    </div>
</x-layout-publico>
