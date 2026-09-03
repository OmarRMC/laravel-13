@php($categoria = $categoria ?? null)

<div class="space-y-6">
    <div>
        <x-input-label for="nombre" value="Nombre" />
        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full"
                      :value="old('nombre', $categoria?->nombre)" required autofocus />
        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            El <code>slug</code> se genera solo a partir del nombre.
        </p>
    </div>

    <div>
        <x-input-label for="color" value="Color" />
        <input id="color" name="color" type="color"
               value="{{ old('color', $categoria?->color ?? '#4f46e5') }}"
               class="mt-1 h-10 w-20 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
        <x-input-error :messages="$errors->get('color')" class="mt-2" />
    </div>
</div>
