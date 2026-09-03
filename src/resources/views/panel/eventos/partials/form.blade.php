@php($evento = $evento ?? null)

<div class="grid gap-6 sm:grid-cols-2">

    <div class="sm:col-span-2">
        <x-input-label for="titulo" value="Título" />
        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" maxlength="150"
                      :value="old('titulo', $evento?->titulo)" required autofocus />
        <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="descripcion" value="Descripción" />
        <textarea id="descripcion" name="descripcion" rows="5" required
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion', $evento?->descripcion) }}</textarea>
        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="categoria_id" value="Categoría" />
        <select id="categoria_id" name="categoria_id" required
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}"
                    @selected(old('categoria_id', $evento?->categoria_id) == $categoria->id)>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="modalidad" value="Modalidad" />
        <select id="modalidad" name="modalidad" required
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (['presencial', 'virtual', 'hibrido'] as $modalidad)
                <option value="{{ $modalidad }}" @selected(old('modalidad', $evento?->modalidad) === $modalidad)>
                    {{ ucfirst($modalidad) }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('modalidad')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="inicia_el" value="Empieza" />
        <x-text-input id="inicia_el" name="inicia_el" type="datetime-local" class="mt-1 block w-full"
                      :value="old('inicia_el', $evento?->inicia_el?->format('Y-m-d\TH:i'))" required />
        <x-input-error :messages="$errors->get('inicia_el')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="termina_el" value="Termina (opcional)" />
        <x-text-input id="termina_el" name="termina_el" type="datetime-local" class="mt-1 block w-full"
                      :value="old('termina_el', $evento?->termina_el?->format('Y-m-d\TH:i'))" />
        <x-input-error :messages="$errors->get('termina_el')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="lugar" value="Lugar" />
        <x-text-input id="lugar" name="lugar" type="text" class="mt-1 block w-full" maxlength="150"
                      :value="old('lugar', $evento?->lugar)" required />
        <x-input-error :messages="$errors->get('lugar')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cupo" value="Cupo" />
        <x-text-input id="cupo" name="cupo" type="number" min="1" class="mt-1 block w-full"
                      :value="old('cupo', $evento?->cupo ?? 15)" required />
        <x-input-error :messages="$errors->get('cupo')" class="mt-2" />
    </div>

    <div>
        <label class="inline-flex items-center mt-6">
            {{-- El hidden hace que el checkbox desmarcado llegue como "0" y no ausente. --}}
            <input type="hidden" name="es_gratuito" value="0">
            <input type="checkbox" name="es_gratuito" value="1"
                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                   @checked(old('es_gratuito', $evento?->es_gratuito ?? true))>
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Evento gratuito</span>
        </label>
    </div>

    <div>
        <x-input-label for="precio" value="Precio (si no es gratuito)" />
        <x-text-input id="precio" name="precio" type="number" step="0.01" min="0" class="mt-1 block w-full"
                      :value="old('precio', $evento?->precio)" />
        <x-input-error :messages="$errors->get('precio')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="estado" value="Estado" />
        <select id="estado" name="estado" required
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (['borrador', 'publicado', 'cerrado', 'cancelado'] as $estado)
                <option value="{{ $estado }}" @selected(old('estado', $evento?->estado ?? 'borrador') === $estado)>
                    {{ ucfirst($estado) }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('estado')" class="mt-2" />
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Solo los eventos <strong>publicados</strong> salen en el catálogo.
        </p>
    </div>
</div>
