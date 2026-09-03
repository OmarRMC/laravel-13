@props(['evento'])

<article class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden flex flex-col">
    <div class="p-5 flex-1">
        <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">
                {{ $evento->categoria->nombre }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ $evento->modalidad }}
            </span>
        </div>

        <h3 class="font-semibold leading-snug">
            <a href="{{ route('eventos.show', $evento) }}" class="hover:underline">
                {{ $evento->titulo }}
            </a>
        </h3>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ $evento->inicia_el->format('d/m/Y H:i') }} · {{ $evento->lugar }}
        </p>

        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
            {{ Str::limit($evento->descripcion, 120) }}
        </p>
    </div>

    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-sm">
        <span class="font-medium">
            {{ $evento->es_gratuito ? 'Gratuito' : 'Bs '.$evento->precio }}
        </span>

        <a href="{{ route('eventos.show', $evento) }}"
           class="text-indigo-600 dark:text-indigo-400 hover:underline">
            Ver detalle
        </a>
    </div>
</article>
