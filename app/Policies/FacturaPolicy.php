<?php

namespace App\Policies;

use App\Models\Factura;
use App\Models\User;

class FacturaPolicy
{
    /**
     * Permite consultar todas las facturas únicamente a los administradores.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Permite ver una factura al administrador o al mesero que la emitió.
     */
    public function view(User $user, Factura $factura): bool
    {
        return $user->role === 'admin' || $user->id === $factura->user_id;
    }
}
