<x-app-layout>
    {{-- Encabezado del módulo de consulta de facturas --}}
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Facturas registradas') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Consulta las ventas emitidas, sus clientes y sus totales.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensaje de estado de la sesión --}}
            @if (session('status'))
            <div class="mb-6 rounded-md border border-green-400 bg-green-100 p-4 text-green-700" role="status">
                {{ session('status') }}
            </div>
            @endif

            {{-- Resumen de los resultados que cumplen los filtros seleccionados --}}
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Facturas encontradas</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ $totalFacturas }}
                    </p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Monto registrado</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">
                        ${{ number_format($totalRegistrado, 2, ",", "") }}
                    </p>
                </div>
            </div>

            {{-- Los filtros se envían por GET para conservar una URL consultable --}}
            <form method="GET" action="{{ route('admin.facturas.index') }}" class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-400 bg-red-100 p-4 text-sm text-red-700" role="alert">
                    Revisa los filtros e intenta nuevamente.
                </div>
                @endif

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div class="xl:col-span-2">
                        <x-input-label for="buscar" value="Factura o cliente" />
                        <x-text-input
                            id="buscar"
                            name="buscar"
                            type="search"
                            class="mt-1 block w-full"
                            :value="old('buscar', request('buscar'))"
                            placeholder="FAC-123 o nombre del cliente" />
                    </div>

                    <div>
                        <x-input-label for="identificacion" value="Identificación" />
                        <x-text-input
                            id="identificacion"
                            name="identificacion"
                            type="search"
                            class="mt-1 block w-full"
                            :value="old('identificacion', request('identificacion'))"
                            placeholder="Número de documento" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select
                            id="estado"
                            name="estado"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            @foreach ($estados as $estado)
                            <option
                                value="{{ $estado->value }}"
                                @selected(old('estado', request('estado'))===$estado->value)
                                >
                                {{ $estado->etiqueta() }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="desde" value="Desde" />
                        <x-text-input
                            id="desde"
                            name="desde"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('desde', request('desde'))"
                            :max="now()->toDateString()" />
                    </div>

                    <div>
                        <x-input-label for="hasta" value="Hasta" />
                        <x-text-input
                            id="hasta"
                            name="hasta"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('hasta', request('hasta'))"
                            :max="now()->toDateString()" />
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <x-primary-button>Filtrar facturas</x-primary-button>

                    @if (request()->hasAny(['buscar', 'identificacion', 'estado', 'desde', 'hasta']))
                    <a
                        href="{{ route('admin.facturas.index') }}"
                        class="text-sm font-medium text-gray-600 underline hover:text-gray-900">
                        Limpiar filtros
                    </a>
                    @endif
                </div>
            </form>

            {{-- Tabla responsive con la información principal de cada factura --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Factura</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Cliente</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Atendió</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500">Productos</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Acción</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($facturas as $factura)
                            @php
                            $estadoEstilo = match ($factura->estado) {
                            \App\Enums\EstadoFactura::PAGADA => 'bg-green-100 text-green-800',
                            \App\Enums\EstadoFactura::PENDIENTE => 'bg-amber-100 text-amber-800',
                            \App\Enums\EstadoFactura::ANULADA => 'bg-red-100 text-red-800',
                            };
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                    {{ $factura->numero_factura }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ $factura->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="min-w-48 px-6 py-4 text-sm text-gray-700">
                                    <p class="font-medium text-gray-900">{{ $factura->cliente_nombre }}</p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $factura->cliente_identificacion ?? 'Sin identificación' }}
                                    </p>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ $factura->user->name }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">
                                    {{ number_format((int) $factura->items_sum_cantidad, 0) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                    ${{ $factura->total }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $estadoEstilo }}">
                                        {{ $factura->estado->etiqueta() }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <a
                                        href="{{ route('facturas.show', $factura) }}"
                                        class="text-indigo-600 hover:text-indigo-900">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No hay facturas que coincidan con la consulta.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- La paginación conserva los filtros aplicados en la URL --}}
                @if ($facturas->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $facturas->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>