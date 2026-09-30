<?php

namespace App\Http\Requests;

use App\Enums\TipoBebida;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBebidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Se integrará con Policies en la Fase 5
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug'      => Str::slug($this->input('nombre')),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        // Obtenemos la bebida inyectada desde el parámetro de la ruta
        $bebida = $this->route('bebida');

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                // Ignora el ID de la bebida actual para evitar que la validación falle al no cambiar el nombre
                Rule::unique('bebidas', 'nombre')->ignore($bebida?->id)
            ],
            'slug' => [
                'required',
                'string',
                'max:120',
                Rule::unique('bebidas', 'slug')->ignore($bebida?->id)
            ],
            'tipo'         => ['required', Rule::enum(TipoBebida::class)], // Valida contra el Enum[cite: 1]
            'precio'       => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'stock'        => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'is_active'    => ['boolean'],
        ];
    }
}
