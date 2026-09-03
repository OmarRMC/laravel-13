<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Hola, {{ auth()->user()->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />
            <div class="grid gap-4 sm:grid-cols-3">
                <a href="{{ route('eventos.index') }}"
                   class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 hover:shadow transition">
                    <h3 class="font-semibold">Catálogo</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buscar eventos e inscribirse</p>
                </a>

                @can('crear eventos')
                    <a href="{{ route('panel.eventos.index') }}"
                       class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 hover:shadow transition">
                        <h3 class="font-semibold">Panel del organizador</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mis eventos e inscritos</p>
                    </a>
                @endcan

                @can('ver-panel-admin')
                    <a href="{{ route('admin.categorias.index') }}"
                       class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 hover:shadow transition">
                        <h3 class="font-semibold">Administración</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Categorías y usuarios</p>
                    </a>
                @endcan
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Mis próximas inscripciones</h3>
                    <a href="{{ route('inscripciones.index') }}"
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        Ver todas
                    </a>
                </div>

                @forelse ($inscripciones as $evento)
                    <div class="flex items-center justify-between py-3 border-t border-gray-100 dark:border-gray-700">
                        <div>
                            <a href="{{ route('eventos.show', $evento) }}" class="font-medium hover:underline">
                                {{ $evento->titulo }}
                            </a>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $evento->inicia_el->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <x-estado-badge :estado="$evento->pivot->estado" />
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Sin inscripciones todavía.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
