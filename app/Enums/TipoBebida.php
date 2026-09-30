<?php

namespace App\Enums;

enum TipoBebida: string
{
    case REFRESCO = 'refresco';
    case CERVEZA = 'cerveza';
    case COCTEL = 'coctel';
    case AGUA = 'agua';

    public function label(): string
    {
        return match ($this) {
            self::REFRESCO => 'Bebida Carbonatada / Refresco',
            self::CERVEZA  => 'Cerveza Nacional o Importada',
            self::COCTEL   => 'Cóctel con Alcohol',
            self::AGUA     => 'Agua Mineral / Natural',
        };
    }
}
