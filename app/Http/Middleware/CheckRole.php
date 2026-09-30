<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Verifica si el usuario está autenticado y si su rol coincide con el requerido
        if (! $request->user() || $request->user()->role !== $role) {
            abort(403, 'Acceso no autorizado para este perfil.');
        }

        return $next($request);
    }
}
