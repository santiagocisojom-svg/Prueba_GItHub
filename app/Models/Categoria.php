<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function bebidas(): BelongsToMany
    {
        return $this->belongsToMany(Bebida::class);
    }
}
