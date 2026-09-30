<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaItem extends Model
{
    protected $fillable = [
        'factura_id',
        'bebida_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    /**
     * Convierte los importes a decimales con dos posiciones.
     */
    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function bebida(): BelongsTo
    {
        return $this->belongsTo(Bebida::class);
    }
}
