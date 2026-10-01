<?php

namespace App\Http\Requests;

use App\Enums\TipoBebida;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreBebidaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {

        return true;
    }

    /**
     * Prepara y transforma los datos antes de que se ejecute la validación.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Genera el slug automáticamente (ej: "Coca Cola" -> "coca-cola")
            'slug' => Str::slug($this->input('nombre')),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Reglas de validación que debe cumplir la petición.
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:bebidas,nombre'],
            'slug' => ['required', 'string', 'max:120', 'unique:bebidas,slug'],
            'tipo' => ['required', Rule::enum(TipoBebida::class)], // Valida contra el Backed Enum
            'precio' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            'categorias' => ['required', 'array', 'min:1'],
            'categorias.*' => ['exists:categorias,id'],
            'is_active' => ['boolean'],
        ];
    }
}
