<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mis inscripciones
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash />

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                @if ($inscripciones->isEmpty())
                    <div class="p-10 text-center text-gray-500 dark:text-gray-400">
                        Todavía no te has inscrito en ningún evento.
                        <a href="{{ route('eventos.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                            Ver el catálogo
                        </a>
                    </div>
                @else
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Evento</th>
                                <th class="px-6 py-3">Fecha</th>
                                <th class="px-6 py-3">Código</th>
                                <th class="px-6 py-3">Estado</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($inscripciones as $evento)
                                <tr>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('eventos.show', $evento) }}" class="font-medium hover:underline">
                                            {{ $evento->titulo }}
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $evento->categoria->nombre }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ $evento->inicia_el->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 font-mono text-xs">{{ $evento->pivot->codigo }}</td>
                                    <td class="px-6 py-4">
                                        <x-estado-badge :estado="$evento->pivot->estado" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if ($evento->pivot->asistio)
                                            {{-- Ruta 27: el certificado solo existe con asistencia registrada. --}}
                                            <a href="{{ route('certificado', $evento) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Certificado
                                            </a>
                                        @elseif ($evento->pivot->estado !== 'cancelada')
                                            <form method="POST" action="{{ route('inscripciones.cancelar', $evento) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="text-red-600 dark:text-red-400 hover:underline">
                                                    Cancelar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4">
                        {{ $inscripciones->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
