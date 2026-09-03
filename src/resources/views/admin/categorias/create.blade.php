<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Nueva categoría
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.categorias.store') }}">
                    @csrf

                    @include('admin.categorias.partials.form', ['categoria' => null])

                    <div class="mt-8 flex items-center gap-4">
                        <x-primary-button>Crear</x-primary-button>
                        <a href="{{ route('admin.categorias.index') }}"
                           class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
