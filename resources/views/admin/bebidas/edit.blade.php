<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Bebida: ') }} {{ $bebida->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('admin.bebidas.update', $bebida) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT') {{-- Spoofing de método HTTP para peticiones de actualización --}}

                    <div>
                        <x-input-label for="nombre" value="Nombre de la Bebida" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $bebida->nombre)" required />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="categorias" value="Categorías" />
                        <select id="categorias" name="categorias[]" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ in_array($categoria->id, old('categorias', $bebida->categorias->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('categorias')" class="mt-2" />
                        <p class="mt-1 text-sm text-gray-500">Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples categorías.</p>
                    </div>

                    <div>
                        <x-input-label for="tipo" value="Tipo de Bebida" />
                        <select id="tipo" name="tipo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->value }}" {{ old('tipo', $bebida->tipo->value) == $tipo->value ? 'selected' : '' }}>
                                {{ $tipo->name }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="precio" value="Precio ($)" />
                            <x-text-input id="precio" name="precio" type="number" step="0.01" class="mt-1 block w-full" :value="old('precio', $bebida->precio)" required />
                            <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="stock" value="Stock Actual" />
                            <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full" :value="old('stock', $bebida->stock)" required />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Actualizar Bebida</x-primary-button>
                        <a href="{{ route('admin.bebidas.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>