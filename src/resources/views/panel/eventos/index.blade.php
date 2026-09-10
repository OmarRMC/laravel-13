<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Events') }}
            </h2>
            <a href="{{ route('panel.eventos.create') }}"
               class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-500">
                {{ __('New Event') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            
            <form method="GET" class="flex flex-wrap gap-2 text-sm">
                @foreach (['' => __('All'), 'borrador' => __('Draft'), 'publicado' => __('Published'), 'cerrado' => __('Closed'), 'cancelado' => __('Cancelled')] as $valor => $etiqueta)
                    <a href="{{ route('panel.eventos.index', $valor ? ['estado' => $valor] : []) }}"
                       class="px-3 py-1.5 rounded-full {{ request('estado') === ($valor ?: null) ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        {{ $etiqueta }}
                    </a>
                @endforeach
            </form>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                @if ($eventos->isEmpty())
                    <div class="p-10 text-center text-gray-500 dark:text-gray-400">
                        {{ __('You have not created any events yet.') }}
                    </div>
                @else
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">{{ __('Event') }}</th>
                                <th class="px-6 py-3">{{ __('Date') }}</th>
                                <th class="px-6 py-3">{{ __('Status') }}</th>
                                <th class="px-6 py-3">{{ __('Registrants') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($eventos as $evento)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $evento->titulo }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $evento->categoria->nombre }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ $evento->inicia_el->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4"><x-estado-badge :estado="$evento->estado" /></td>
                                    <td class="px-6 py-4">
                                        {{ $evento->inscritos_count }} / {{ $evento->cupo }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-4">
                                            <a href="{{ route('panel.eventos.inscritos', $evento) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Registrants') }}</a>
                                            <a href="{{ route('panel.eventos.edit', $evento) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Edit') }}</a>

                                            <form method="POST" action="{{ route('panel.eventos.destroy', $evento) }}"
                                                  onsubmit="return confirm('{{ __('This will also delete its registrations. Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 dark:text-red-400 hover:underline">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4">{{ $eventos->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
