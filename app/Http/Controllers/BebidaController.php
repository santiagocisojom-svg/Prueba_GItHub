<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBebidaRequest;
use App\Http\Requests\UpdateBebidaRequest;
use App\Models\Bebida;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use App\Models\Categoria;
use App\Enums\TipoBebida;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;

class BebidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Carga ansiosa ('with'), filtro reutilizable ('disponibles') y paginación
        $bebidas = Bebida::with('categoria')

            ->latest()
            ->paginate(15);

        return view('admin.bebidas.index', compact('bebidas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Bebida::class);

        $categorias = Categoria::all();
        $tipos = TipoBebida::cases(); // Devuelve las opciones del Enum

        return view('admin.bebidas.create', compact('categorias', 'tipos'));
    }

    /**
     * FUNCION STORE MODIFICADA PARA 
     */

    public function store(StoreBebidaRequest $request)
    {
        // Obtenemos ÚNICAMENTE los datos validados[cite: 1]
        $datos = $request->validated();

        // Asignamos el ID del usuario creador (Administrador en sesión)[cite: 1]
        $datos['user_id'] = $request->user()->id;

        // Persistimos en la base de datos


        Bebida::create($datos);



        return redirect()
            ->route('admin.bebidas.index')
            ->with('status', 'Bebida registrada exitosamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bebida $bebida)
    {
        // Verificación con Gate (Lanza excepción HTTP 403 automáticamente si falla)
        Gate::authorize('update', $bebida);
        $categorias = Categoria::all();
        $tipos = TipoBebida::cases();

        return view('admin.bebidas.edit', compact('bebida', 'categorias', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBebidaRequest $request, Bebida $bebida)
    {
        Gate::authorize('update', $bebida);
        // Actualizamos únicamente con los datos validados[cite: 1]
        $bebida->update($request->validated());

        return redirect()
            ->route('admin.bebidas.index')
            ->with('status', 'Bebida actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bebida $bebida)
    {
        Gate::authorize('delete', $bebida);

        $bebida->delete();

        return redirect()
            ->route('admin.bebidas.index')
            ->with('status', 'Bebida eliminada correctamente.');
    }

    /**
     * Muestra el catálogo de bebidas para el mesero.
     */
    public function pos(Request $request)
    {
        // Consultamos solo las bebidas activas o con stock > 0
        $query = Bebida::query();

        // Filtro de búsqueda opcional por nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $bebidas = $query->orderBy('nombre', 'asc')->paginate(12);

        return view('mesero.pos', compact('bebidas'));
    }
}
