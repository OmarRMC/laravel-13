<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Categorías
            </h2>
            <a href="{{ route('admin.categorias.create') }}"
               class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-500">
                Nueva categoría
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                @if ($categorias->isEmpty())
                    <div class="p-10 text-center text-gray-500 dark:text-gray-400">
                        Sin categorías. Crea la primera para poder publicar eventos.
                    </div>
                @else
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Nombre</th>
                                <th class="px-6 py-3">Slug</th>
                                <th class="px-6 py-3">Eventos</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($categorias as $categoria)
                                <tr>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-2 font-medium">
                                            <span class="h-3 w-3 rounded-full"
                                                  style="background-color: {{ $categoria->color ?? '#9ca3af' }}"></span>
                                            {{ $categoria->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">{{ $categoria->slug }}</td>
                                    <td class="px-6 py-4">{{ $categoria->eventos_count }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-4">
                                            <a href="{{ route('admin.categorias.edit', $categoria) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</a>

                                            {{-- categoria_id es restrictOnDelete: con eventos, el borrado falla. --}}
                                            @if ($categoria->eventos_count === 0)
                                                <form method="POST" action="{{ route('admin.categorias.destroy', $categoria) }}"
                                                      onsubmit="return confirm('¿Eliminar esta categoría?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">Tiene eventos</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4">{{ $categorias->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
