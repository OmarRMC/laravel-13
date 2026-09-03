<x-layout-publico :titulo="$categoria?->nombre ?? 'Eventos'">
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">
            {{ $categoria?->nombre ?? 'Próximos eventos' }}
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $eventos->total() }} {{ Str::plural('evento', $eventos->total()) }} con inscripción abierta
        </p>
    </x-slot>

    <nav class="flex flex-wrap gap-2 mb-8 text-sm">
        <a href="{{ route('eventos.index') }}"
           class="px-3 py-1.5 rounded-full {{ $categoria === null ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
            Todas
        </a>

        @foreach ($categorias as $cat)
            <a href="{{ route('eventos.categoria', $cat) }}"
               class="px-3 py-1.5 rounded-full {{ $categoria?->is($cat) ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                {{ $cat->nombre }}
            </a>
        @endforeach
    </nav>

    @if ($eventos->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg p-10 text-center text-gray-500 dark:text-gray-400">
            Todavía no hay eventos publicados en esta sección.
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($eventos as $evento)
                <x-evento-card :$evento />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $eventos->links() }}
        </div>
    @endif
</x-layout-publico>
