<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Additional profile details: phone, institution, avatar and bio.') }}
                    </p>
                </header>

                <form method="post" action="{{ route('perfil.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <div class="flex items-center gap-4" x-data="{ preview: null }">
                        <template x-if="preview">
                            <img :src="preview" alt="Avatar" class="h-16 w-16 rounded-full object-cover">
                        </template>

                        <template x-if="!preview">
                            <span>
                                @if ($perfil?->avatar)
                                    <img src="{{ $perfil->avatar_url }}" alt="Avatar"
                                         class="h-16 w-16 rounded-full object-cover">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 text-xl font-medium">
                                        {{ Str::substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </span>
                        </template>

                        <div class="flex-1">
                            <x-input-label for="avatar" :value="__('Avatar')" />
                            <input id="avatar" name="avatar" type="file" accept="image/*"
                                   @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null"
                                   class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-400">
                            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="telefono" :value="__('Phone')" />
                        <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" maxlength="20"
                                      :value="old('telefono', $perfil?->telefono)" />
                        <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                    </div>

                    <div>
                        <x-input-label for="institucion" :value="__('Institution')" />
                        <x-text-input id="institucion" name="institucion" type="text" class="mt-1 block w-full" maxlength="255"
                                      :value="old('institucion', $perfil?->institucion)" />
                        <x-input-error class="mt-2" :messages="$errors->get('institucion')" />
                    </div>

                    <div>
                        <x-input-label for="bio" :value="__('Bio')" />
                        <textarea id="bio" name="bio" rows="4" maxlength="1000"
                                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('bio', $perfil?->bio) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>

                        @if (session('status') === 'perfil-actualizado')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600 dark:text-gray-400"
                            >{{ __('Saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
