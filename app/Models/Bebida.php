<?php

namespace App\Models;

use App\Enums\TipoBebida;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use App\Casts\PrecioCast;

class Bebida extends Model
{
    protected $fillable = [
        'categoria_id',
        'user_id',
        'nombre',
        'slug',
        'tipo',
        'precio',
        'stock',
        'is_active',
    ];

    // Casteo nativo en Laravel 11 / 12
    protected function casts(): array
    {
        return [
            'tipo'      => TipoBebida::class,
            'precio'    => PrecioCast::class,
            'is_active' => 'boolean',
        ];
    }

    // Atributo fluido para formatear nombre
    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => ucwords(trim($value))
        );
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar únicamente bebidas activas y con stock.
     */
    public function scopeDisponibles(Builder $query): void
    {
        $query->where('is_active', true)->where('stock', '>', 0);
    }

    /**
     * Scope para filtrar bebidas por su tipo Enum.
     */
    public function scopePorTipo(Builder $query, TipoBebida $tipo): void
    {
        $query->where('tipo', $tipo);
    }
}
