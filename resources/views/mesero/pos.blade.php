<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Menú de Bebidas - Toma de Pedidos') }}
            </h2>
            <a href="{{ route('carrito.index') }}" class="relative inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs  uppercase tracking-widest hover:bg-indigo-700">
                Ver Carrito
                @if(session('carrito') && count(session('carrito')) > 0)
                <span class="ml-2 bg-red-500  text-xs px-2 py-0.5 rounded-full font-bold">
                    {{ array_sum(array_column(session('carrito'), 'cantidad')) }}
                </span>
                @endif
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensaje de éxito al agregar --}}
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
            @endif

            {{-- Buscador de bebidas --}}
            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                <form action="{{ route('pos.index') }}" method="GET" class="flex gap-4">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar bebida por nombre..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Buscar
                    </button>
                    @if(request('buscar'))
                    <a href="{{ route('pos.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 flex items-center">
                        Limpiar
                    </a>
                    @endif
                </form>
            </div>

            {{-- Rejilla de Bebidas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($bebidas as $bebida)
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 flex flex-col justify-between p-5">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-gray-800">{{ $bebida->nombre }}</h3>
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                ${{ $bebida->precio }}
                            </span>
                        </div>
                        <p class="text-gray-500 text-sm mb-4">
                            {{ $bebida->descripcion ?? 'Sin descripción disponible.' }}
                        </p>
                    </div>

                    <div>
                        <div class="text-xs text-gray-400 mb-2">
                            Stock disponible: <span class="font-bold text-gray-600">{{ $bebida->stock ?? 'N/A' }}</span>
                        </div>

                        <form action="{{ route('carrito.agregar', $bebida->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700  font-bold py-2 px-4 rounded text-sm transition duration-150 flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Agregar al Carrito
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-full bg-white p-8 text-center text-gray-500 rounded-lg shadow-sm">
                    No se encontraron bebidas disponibles en el catálogo.
                </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $bebidas->links() }}
            </div>

        </div>
    </div>
</x-app-layout>