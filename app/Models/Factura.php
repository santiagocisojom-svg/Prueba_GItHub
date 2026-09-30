<?php

namespace App\Models;

use App\Enums\EstadoFactura;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Casts\PrecioCast;

class Factura extends Model
{
    protected $fillable = [
        'user_id',
        'numero_factura',
        'cliente_nombre',
        'cliente_identificacion',
        'subtotal',
        'impuesto',
        'total',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoFactura::class, // Eloquent convierte de String a Enum automáticamente
            'subtotal' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'total' => PrecioCast::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FacturaItem::class);
    }
}
