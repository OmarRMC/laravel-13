<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Nuevo evento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('panel.eventos.store') }}">
                    @csrf

                    @include('panel.eventos.partials.form', ['evento' => null])

                    <div class="mt-8 flex items-center gap-4">
                        <x-primary-button>Crear evento</x-primary-button>
                        <a href="{{ route('panel.eventos.index') }}"
                           class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
