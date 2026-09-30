<?php

namespace App\Enums;

enum EstadoFactura: string
{
    case PAGADA = 'pagada';
    case ANULADA = 'anulada';
    case PENDIENTE = 'pendiente';

    /**
     * Devuelve el estado con una capitalización adecuada para la interfaz.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::PAGADA => 'Pagada',
            self::ANULADA => 'Anulada',
            self::PENDIENTE => 'Pendiente',
        };
    }
}
