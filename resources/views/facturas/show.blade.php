<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Factura: ') }} {{ $factura->numero_factura }}
            </h2>
            @can('viewAny', App\Models\Factura::class)
            <a href="{{ route('admin.facturas.index') }}" class="px-4 py-2 bg-gray-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-gray-700">
                &larr; Volver a facturas
            </a>
            @else
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-gray-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-gray-700">
                &larr; Volver al Menú
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('status') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                {{-- Encabezado de la Factura --}}
                <div class="flex justify-between items-start border-b pb-6 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">SISTEMA DE BEBIDAS</h1>
                        <p class="text-sm text-gray-500">Comprobante Digital de Venta</p>
                    </div>
                    <div class="text-right">
                        @php
                        $estadoEstilo = match ($factura->estado) {
                        \App\Enums\EstadoFactura::PAGADA => 'bg-green-100 text-green-800',
                        \App\Enums\EstadoFactura::PENDIENTE => 'bg-amber-100 text-amber-800',
                        \App\Enums\EstadoFactura::ANULADA => 'bg-red-100 text-red-800',
                        };
                        @endphp

                        <span class="inline-block px-3 py-1 text-xs font-bold uppercase rounded-full mb-2 {{ $estadoEstilo }}">
                            {{ $factura->estado->etiqueta() }}
                        </span>
                        <p class="text-sm text-gray-600"><strong>Fecha:</strong> {{ $factura->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                {{-- Información del Cliente y Atención --}}
                <div class="grid grid-cols-2 gap-6 mb-8 text-sm">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">Datos del Cliente</h3>
                        <p><strong>Nombre:</strong> {{ $factura->cliente_nombre }}</p>
                        <p><strong>Identificación:</strong> {{ $factura->cliente_identificacion ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2 border-b pb-1">Atendido Por</h3>
                        <p><strong>Mesero:</strong> {{ $factura->user?->name ?? 'Usuario del Sistema' }}</p>
                        <p><strong>N° Factura:</strong> {{ $factura->numero_factura }}</p>
                    </div>
                </div>

                {{-- Tabla de Ítems Consumidos --}}
                <div class="mb-8">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50 text-xs font-semibold text-gray-600 uppercase">
                                <th class="p-3">Bebida</th>
                                <th class="p-3 text-center">Cantidad</th>
                                <th class="p-3 text-right">Precio Unitario</th>
                                <th class="p-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @foreach ($factura->items as $item)
                            <tr>
                                <td class="p-3 font-medium text-gray-800">{{ $item->bebida->nombre ?? 'Producto no disponible' }}</td>
                                <td class="p-3 text-center">{{ $item->cantidad }}</td>
                                <td class="p-3 text-right">${{ $item->precio_unitario }}</td>
                                <td class="p-3 text-right font-semibold">${{ $item->subtotal }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Desglose de Totales --}}
                <div class="flex justify-end">
                    <div class="w-1/2 space-y-2 text-sm border-t pt-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span>${{ number_format($factura->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Impuesto (16%):</span>
                            <span>${{ number_format($factura->impuesto, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-900 border-t pt-2">
                            <span>Total Pagado:</span>
                            <span>${{ $factura->total }}</span>
                        </div>
                    </div>
                </div>

                {{-- Acción para la Generación de PDF --}}
                <div class="mt-8 pt-6 border-t flex justify-end">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Descargar PDF Factura
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>