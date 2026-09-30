<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bebida;

class CarritoController extends Controller
{
    /**
     * Muestra la vista del carrito con el desglose de productos y totales.
     */
    public function index()
    {
        $carrito = session()->get('carrito', []);

        // Calculamos subtotal e impuestos dinámicamente
        $subtotal = array_reduce($carrito, fn($acc, $item) => $acc + (int)$item['subtotal'], 0);
        $impuesto = $subtotal * 0.16; // Ejemplo: IVA del 16%
        $total = $subtotal + $impuesto;

        return view('carrito.index', compact('carrito', 'subtotal', 'impuesto', 'total'));
    }

    /**
     * Agrega una bebida al carrito o incrementa su cantidad.
     */
    public function agregar(Request $request, Bebida $bebida)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$bebida->id])) {
            $carrito[$bebida->id]['cantidad']++;
            $carrito[$bebida->id]['subtotal'] = $carrito[$bebida->id]['cantidad'] * $carrito[$bebida->id]['precio'];
        } else {
            $carrito[$bebida->id] = [
                'id' => $bebida->id,
                'nombre' => $bebida->nombre,
                'precio' => number_format((float) $bebida->precio, 2, '.', ''),
                'cantidad' => 1,
                'subtotal' => number_format((float) $bebida->precio, 2, '.', ''),
            ];
        }

        session()->put('carrito', $carrito);

        return redirect()->back()->with('success', "{$bebida->nombre} agregada al carrito.");
    }
    /**
     * Actualiza la cantidad de una bebida en el carrito.
     */
    public function actualizar(Request $request, Bebida $bebida)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$bebida->id])) {
            $carrito[$bebida->id]['cantidad'] = $request->cantidad;
            $carrito[$bebida->id]['subtotal'] = $request->cantidad * (int)$carrito[$bebida->id]['precio'];
            session()->put('carrito', $carrito);
        }

        return redirect()->route('carrito.index')->with('success', 'Cantidad actualizada correctamente.');
    }

    /**
     * Elimina un ítem específico del carrito.
     */
    public function eliminar(Bebida $bebida)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$bebida->id])) {
            unset($carrito[$bebida->id]);
            session()->put('carrito', $carrito);
        }

        return redirect()->route('carrito.index')->with('success', 'Producto eliminado del carrito.');
    }

    /**
     * Vacía completamente el carrito en sesión.
     */
    public function vaciar()
    {
        session()->forget('carrito');
        return redirect()->route('carrito.index')->with('success', 'Carrito vaciado.');
    }
}
