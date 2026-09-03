<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Inscritos · {{ $evento->titulo }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $inscritos->total() }} de {{ $evento->cupo }} plazas
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="flex flex-wrap gap-3 text-sm">
                <a href="{{ route('panel.eventos.pdf', $evento) }}"
                   class="px-3 py-1.5 rounded-md bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Descargar PDF
                </a>
                <a href="{{ route('panel.eventos.excel', $evento) }}"
                   class="px-3 py-1.5 rounded-md bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Descargar Excel
                </a>
                <a href="{{ route('panel.eventos.index') }}"
                   class="px-3 py-1.5 text-gray-600 dark:text-gray-400 hover:underline">
                    Volver
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                @if ($inscritos->isEmpty())
                    <div class="p-10 text-center text-gray-500 dark:text-gray-400">
                        Nadie se ha inscrito todavía.
                    </div>
                @else
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Participante</th>
                                <th class="px-6 py-3">Código</th>
                                <th class="px-6 py-3">Estado</th>
                                <th class="px-6 py-3">Asistencia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($inscritos as $inscrito)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $inscrito->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $inscrito->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">{{ $inscrito->pivot->codigo }}</td>
                                    <td class="px-6 py-4"><x-estado-badge :estado="$inscrito->pivot->estado" /></td>
                                    <td class="px-6 py-4">
                                        {{-- Ruta 35: el parámetro se llama {inscrito} por el scopeBindings(). --}}
                                        <form method="POST"
                                              action="{{ route('panel.eventos.asistencia', [$evento, $inscrito]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-sm {{ $inscrito->pivot->asistio ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }} hover:underline">
                                                {{ $inscrito->pivot->asistio ? 'Asistió' : 'Marcar asistencia' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4">{{ $inscritos->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
