<?php

namespace App\Http\Controllers;

use App\Enums\EstadoFactura;
use App\Http\Requests\IndexFacturaRequest;
use App\Models\Bebida;
use App\Models\Factura;
use App\Models\FacturaItem;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class FacturaController extends Controller
{
    /**
     * Cantidad de facturas mostradas en cada página.
     */
    private const PAGINAR_POR_PAGINA = 15;

    /**
     * Muestra las facturas registradas y permite filtrar sus resultados.
     */
    public function index(IndexFacturaRequest $request): View
    {
        $filtros = $request->filtros();
        $consulta = Factura::query();

        // La búsqueda puede coincidir con el número de factura, el cliente o su identificación.
        if (! empty($filtros['buscar'])) {
            $termino = $filtros['buscar'];

            $consulta->where(function (Builder $consultaInterna) use ($termino): void {
                $consultaInterna
                    ->where('numero_factura', 'like', "%{$termino}%")
                    ->orWhere('cliente_nombre', 'like', "%{$termino}%")
                    ->orWhere('cliente_identificacion', 'like', "%{$termino}%");
            });
        }

        // Este filtro permite localizar documentos de identidad específicos.
        if (! empty($filtros['identificacion'])) {
            $consulta->where('cliente_identificacion', 'like', "%{$filtros['identificacion']}%");
        }

        // Se filtra directamente por el valor respaldado del enum de estado.
        if (! empty($filtros['estado'])) {
            $consulta->where('estado', $filtros['estado']);
        }

        // Las fechas incluyen tanto el día inicial como el final del periodo.
        if (! empty($filtros['desde'])) {
            $consulta->whereDate('created_at', '>=', $filtros['desde']);
        }

        if (! empty($filtros['hasta'])) {
            $consulta->whereDate('created_at', '<=', $filtros['hasta']);
        }

        // Se evitan consultas N+1 al mostrar el emisor y la cantidad de productos.
        $facturas = (clone $consulta)
            ->with('user')
            ->withSum('items', 'cantidad')
            ->latest()
            ->paginate(self::PAGINAR_POR_PAGINA)
            ->withQueryString();

        // El resumen representa únicamente los resultados que cumplen los filtros.
        $totalFacturas = (clone $consulta)->count();
        $totalRegistrado = (clone $consulta)->sum('total');
        $estados = EstadoFactura::cases();

        return view('admin.facturas.index', compact(
            'facturas',
            'estados',
            'totalFacturas',
            'totalRegistrado',
        ));
    }

    /**
     * Procesa la compra del carrito, crea la factura e ítems, y descuenta el stock.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_nombre' => 'required|string|max:255',
            'cliente_identificacion' => 'nullable|string|max:50',
        ]);

        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('pos.index')->with('error', 'El carrito está vacío.');
        }

        try {
            // Iniciamos la transacción atómica
            $factura = DB::transaction(function () use ($request, $carrito) {

                $subtotal = array_reduce($carrito, fn($acc, $item) => $acc + (float)$item['subtotal'], 0);
                $impuesto = (float)$subtotal * 0.16;
                $total = $subtotal + $impuesto;

                // 1. Crear la cabecera de la Factura
                $factura = Factura::create([
                    'user_id' => Auth::id(), // Mesero emisor
                    'numero_factura' => 'FAC-' . strtoupper(Str::random(8)),
                    'cliente_nombre' => $request->cliente_nombre,
                    'cliente_identificacion' => $request->cliente_identificacion,
                    'subtotal' => (float) $subtotal,
                    'impuesto' => (float) $impuesto,
                    'total' => (float) $total,
                    'estado' => EstadoFactura::PAGADA,
                ]);

                // 2. Procesar cada producto del carrito
                foreach ($carrito as $item) {
                    $bebida = Bebida::findOrFail($item['id']);

                    // Validar stock antes de descontar
                    if ($bebida->stock < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para la bebida: {$bebida->nombre}");
                    }

                    // Registrar detalle en factura_items
                    FacturaItem::create([
                        'factura_id' => $factura->id,
                        'bebida_id' => $bebida->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Descontar inventario
                    $bebida->decrement('stock', $item['cantidad']);
                }

                return $factura;
            });

            // 3. Si la transacción fue exitosa, vaciamos el carrito
            session()->forget('carrito');

            return redirect()->route('facturas.show', $factura)
                ->with('status', 'Venta procesada y factura generada exitosamente.');
        } catch (\Exception $e) {
            // En caso de error, DB::transaction hace rollback automáticamente
            return redirect()->route('carrito.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Muestra el detalle de la factura generada.
     */
    public function show(Factura $factura)
    {
        // La política impide que un mesero consulte facturas de otros usuarios.
        Gate::authorize('view', $factura);

        // Carga ansiosa para mostrar el mesero y los productos consumidos
        $factura->load(['user', 'items.bebida']);

        return view('facturas.show', compact('factura'));
    }
}
