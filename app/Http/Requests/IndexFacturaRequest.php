<?php

namespace App\Http\Requests;

use App\Enums\EstadoFactura;
use App\Models\Factura;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexFacturaRequest extends FormRequest
{
    /**
     * Autoriza la consulta cuando el usuario puede ver el listado de facturas.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Factura::class) ?? false;
    }

    /**
     * Elimina espacios sobrantes de los filtros de texto antes de validarlos.
     */
    protected function prepareForValidation(): void
    {
        foreach (['buscar', 'identificacion'] as $campo) {
            $valor = $this->input($campo);

            if (is_string($valor)) {
                $this->merge([$campo => trim($valor)]);
            }
        }
    }

    /**
     * Valida y normaliza los filtros enviados desde el formulario de consulta.
     */
    public function rules(): array
    {
        return [
            'buscar' => ['nullable', 'string', 'max:100'],
            'identificacion' => ['nullable', 'string', 'max:50'],
            'estado' => ['nullable', Rule::enum(EstadoFactura::class)],
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => [
                'nullable',
                'date_format:Y-m-d',
                Rule::when(
                    $this->filled('desde') && $this->filled('hasta'),
                    'after_or_equal:desde',
                ),
            ],
        ];
    }

    /**
     * Devuelve únicamente los filtros validados para la consulta.
     */
    public function filtros(): array
    {
        return array_filter(
            $this->validated(),
            static fn (mixed $valor): bool => $valor !== null && $valor !== '',
        );
    }
}
