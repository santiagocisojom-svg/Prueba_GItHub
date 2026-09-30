<?php

namespace App\Policies;

use App\Models\Bebida;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BebidaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null; // Si no es admin, continúa evaluando las reglas individuales
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bebida $bebida): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    /**
     * Determina si el usuario puede actualizar la bebida.
     */
    public function update(User $user, Bebida $bebida): bool
    {
        // Solo permite la edición si el usuario es el creador del registro
        return $user->id === $bebida->user_id;
    }

    /**
     * Determina si el usuario puede eliminar la bebida.
     */
    public function delete(User $user, Bebida $bebida): bool
    {
        return $user->id === $bebida->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bebida $bebida): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bebida $bebida): bool
    {
        return false;
    }
}
