<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Carrito de Compras / Venta Actual') }}
        </h2>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(count($carrito) > 0)
                <table class="min-w-full divide-y divide-gray-200 mb-6">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bebida</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Unitario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($carrito as $id => $item)
                        <tr>
                            <td class="px-6 py-4">{{ $item['nombre'] }}</td>
                            <td class="px-6 py-4">${{ $item['precio'] }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('carrito.actualizar', $id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="cantidad" value="{{ $item['cantidad'] }}" min="1" class="w-20 rounded border-gray-300 py-1 text-sm">
                                    <button type="submit" class="text-xs bg-gray-600 px-2 py-1 rounded">Actualizar</button>
                                </form>
                            </td>
                            <td class="px-6 py-4">${{$item['subtotal']}}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('carrito.eliminar', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Resumen de totales --}}
                <div class="flex justify-end mb-6">
                    <div class="w-1/3 bg-gray-50 p-4 rounded shadow-inner space-y-2">
                        <div class="flex justify-between"><span>Subtotal:</span> <strong>${{ number_format($subtotal, 2) }}</strong></div>
                        <div class="flex justify-between"><span>Impuesto (16%):</span> <strong>${{ number_format($impuesto, 2) }}</strong></div>
                        <div class="flex justify-between text-lg text-green-700 font-bold border-t pt-2"><span>Total:</span> <span>${{ number_format($total, 2) }}</span></div>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <form action="{{ route('carrito.vaciar') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-700  font-bold py-2 px-4 rounded">
                            Vaciar Carrito
                        </button>
                    </form>

                    {{-- Botón para pasar a la confirmación de la factura --}}
                    <form action="{{ route('facturas.store') }}" method="POST" class="mt-6 bg-gray-50 p-4 rounded-lg">
                        @csrf
                        <h3 class="font-bold text-gray-700 mb-3">Datos del Cliente para la Factura</h3>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="cliente_nombre" value="Nombre del Cliente *" />
                                <x-text-input id="cliente_nombre" name="cliente_nombre" type="text" class="w-full mt-1" required placeholder="Ej: Juan Pérez" />
                            </div>
                            <div>
                                <x-input-label for="cliente_identificacion" value="Identificación / RIF / Cédula" />
                                <x-text-input id="cliente_identificacion" name="cliente_identificacion" type="text" class="w-full mt-1" placeholder="Ej: V-12345678" />
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700  font-bold py-3 rounded text-center">
                            Confirmar Pago y Emitir Factura
                        </button>
                    </form>
                </div>
                @else
                <p class="text-gray-500 text-center py-8">El carrito está vacío. Agrega bebidas para iniciar una venta.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>