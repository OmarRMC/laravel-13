{{-- Ruta 45: roles (sync sobre role_user) y baja lógica (activo). --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $usuario->name }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $usuario->email }}</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
                    @csrf
                    @method('PUT')

                    <fieldset>
                        <legend class="font-medium mb-3">{{ __('Roles') }}</legend>

                        <div class="space-y-2">
                            @foreach ($roles as $rol)
                                <label class="flex items-start gap-2">
                                    <input type="checkbox" name="roles[]" value="{{ $rol->id }}"
                                           class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @checked(in_array($rol->id, old('roles', $usuario->roles->pluck('id')->all())))>
                                    <span>
                                        <span class="text-sm font-medium">{{ $rol->nombre }}</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $rol->descripcion }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                    </fieldset>

                    <fieldset class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <legend class="font-medium mb-3">{{ __('Access') }}</legend>

                        <label class="inline-flex items-center">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                   @checked(old('activo', $usuario->activo))>
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Active account') }}</span>
                        </label>

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {!! __('Unchecking it makes the :field middleware log the user out on the next request. Nothing is deleted: their events and registrations remain.', ['field' => '<code>activo</code>']) !!}
                        </p>
                    </fieldset>

                    <div class="mt-8 flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('admin.usuarios.index') }}"
                           class="text-sm text-gray-600 dark:text-gray-400 hover:underline">{{ __('Back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
