<?php

namespace Tests\Feature;

use App\Enums\EstadoFactura;
use App\Models\Factura;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacturaIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_los_visitantes_no_pueden_consultar_las_facturas(): void
    {
        $this->get(route('admin.facturas.index'))
            ->assertRedirect(route('login'));
    }

    public function test_los_meseros_no_pueden_consultar_todas_las_facturas(): void
    {
        $mesero = User::factory()->create(['role' => 'mesero']);

        $this->actingAs($mesero)
            ->get(route('admin.facturas.index'))
            ->assertForbidden();
    }

    public function test_un_administrador_puede_consultar_las_facturas_registradas(): void
    {
        $administrador = User::factory()->create(['role' => 'admin']);
        $factura = $this->crearFactura($administrador, [
            'numero_factura' => 'FAC-CONSULTA-001',
            'cliente_nombre' => 'Cliente de prueba',
            'total' => 125.50,
        ]);

        $this->actingAs($administrador)
            ->get(route('admin.facturas.index'))
            ->assertOk()
            ->assertSee('Facturas registradas')
            ->assertSee($factura->numero_factura)
            ->assertSee($factura->cliente_nombre)
            ->assertSee('$125.50');
    }

    public function test_los_filtros_permiten_localizar_una_factura(): void
    {
        $administrador = User::factory()->create(['role' => 'admin']);
        $this->crearFactura($administrador, [
            'numero_factura' => 'FAC-FILTRO-001',
            'cliente_nombre' => 'Ana Pérez',
            'estado' => EstadoFactura::PAGADA,
        ]);
        $this->crearFactura($administrador, [
            'numero_factura' => 'FAC-FILTRO-002',
            'cliente_nombre' => 'Carlos Gómez',
            'estado' => EstadoFactura::ANULADA,
        ]);

        $this->actingAs($administrador)
            ->get(route('admin.facturas.index', [
                'buscar' => 'Ana',
                'estado' => EstadoFactura::PAGADA->value,
            ]))
            ->assertOk()
            ->assertSee('FAC-FILTRO-001')
            ->assertSee('Ana Pérez')
            ->assertDontSee('FAC-FILTRO-002')
            ->assertDontSee('Carlos Gómez');
    }

    public function test_un_mesero_solo_puede_ver_su_propia_factura(): void
    {
        $mesero = User::factory()->create(['role' => 'mesero']);
        $otroMesero = User::factory()->create(['role' => 'mesero']);
        $facturaPropia = $this->crearFactura($mesero);
        $facturaAjena = $this->crearFactura($otroMesero);

        $this->actingAs($mesero)
            ->get(route('facturas.show', $facturaPropia))
            ->assertOk();

        $this->actingAs($mesero)
            ->get(route('facturas.show', $facturaAjena))
            ->assertForbidden();
    }

    /**
     * Crea una factura válida sin depender de una fábrica adicional.
     */
    private function crearFactura(User $usuario, array $atributos = []): Factura
    {
        return Factura::create(array_merge([
            'user_id' => $usuario->id,
            'numero_factura' => 'FAC-'.fake()->unique()->numerify('########'),
            'cliente_nombre' => 'Cliente General',
            'cliente_identificacion' => '0102030405',
            'subtotal' => 100.00,
            'impuesto' => 16.00,
            'total' => 116.00,
            'estado' => EstadoFactura::PAGADA,
        ], $atributos));
    }
}
