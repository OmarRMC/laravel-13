<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Usuarios
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <form method="GET" class="flex gap-2">
                <x-text-input name="q" type="search" class="w-full sm:w-80"
                              :value="request('q')" placeholder="Buscar por nombre" />
                <x-primary-button>Buscar</x-primary-button>
            </form>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Usuario</th>
                            <th class="px-6 py-3">Roles</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($usuarios as $usuario)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium">{{ $usuario->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $usuario->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($usuario->roles as $rol)
                                            <span class="inline-flex rounded-full bg-indigo-100 dark:bg-indigo-900 px-2 py-0.5 text-xs text-indigo-800 dark:text-indigo-200">
                                                {{ $rol->nombre }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400">Sin rol</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($usuario->activo)
                                        <span class="inline-flex rounded-full bg-green-100 dark:bg-green-900 px-2 py-0.5 text-xs text-green-800 dark:text-green-200">Activo</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-red-100 dark:bg-red-900 px-2 py-0.5 text-xs text-red-800 dark:text-red-200">Desactivado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                                       class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                    Ningún usuario coincide con la búsqueda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4">{{ $usuarios->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
