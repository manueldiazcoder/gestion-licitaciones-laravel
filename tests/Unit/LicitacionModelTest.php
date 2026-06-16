<?php

namespace Tests\Unit;

use App\Models\Licitacion;
use App\Models\Responsable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicitacionModelTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Constantes
    // ─────────────────────────────────────────

    public function test_constante_estados_tiene_los_5_esperados(): void
    {
        $esperados = ['Borrador', 'Publicado', 'En evaluación', 'Adjudicado', 'Cancelado'];

        $this->assertSame($esperados, Licitacion::ESTADOS);
    }

    public function test_constante_colores_estado_tiene_todos_los_estados(): void
    {
        foreach (Licitacion::ESTADOS as $estado) {
            $this->assertArrayHasKey($estado, Licitacion::COLORES_ESTADO);
        }
    }

    // ─────────────────────────────────────────
    //  Scopes
    // ─────────────────────────────────────────

    public function test_scope_search_filtra_por_objeto(): void
    {
        Licitacion::factory()->create(['objeto' => 'Mantenimiento de vías']);
        Licitacion::factory()->create(['objeto' => 'Compra de computadores']);

        $resultados = Licitacion::search('Mantenimiento')->get();

        $this->assertCount(1, $resultados);
        $this->assertSame('Mantenimiento de vías', $resultados->first()->objeto);
    }

    public function test_scope_search_filtra_por_descripcion(): void
    {
        Licitacion::factory()->create(['descripcion' => 'Contrato de obras civiles']);
        Licitacion::factory()->create(['descripcion' => 'Suministro de oficina']);

        $resultados = Licitacion::search('obras')->get();

        $this->assertCount(1, $resultados);
    }

    public function test_scope_search_null_retorna_todos(): void
    {
        Licitacion::factory()->count(3)->create();

        $resultados = Licitacion::search(null)->get();

        $this->assertCount(3, $resultados);
    }

    public function test_scope_by_estado_filtra_correctamente(): void
    {
        Licitacion::factory()->create(['estado' => 'Borrador']);
        Licitacion::factory()->create(['estado' => 'Publicado']);
        Licitacion::factory()->create(['estado' => 'Publicado']);

        $resultados = Licitacion::byEstado('Publicado')->get();

        $this->assertCount(2, $resultados);
    }

    public function test_scope_by_estado_null_retorna_todos(): void
    {
        Licitacion::factory()->count(3)->create();

        $resultados = Licitacion::byEstado(null)->get();

        $this->assertCount(3, $resultados);
    }

    public function test_scope_by_moneda_filtra_correctamente(): void
    {
        Licitacion::factory()->create(['moneda' => 'COP']);
        Licitacion::factory()->create(['moneda' => 'USD']);
        Licitacion::factory()->create(['moneda' => 'USD']);

        $resultados = Licitacion::byMoneda('USD')->get();

        $this->assertCount(2, $resultados);
    }

    public function test_scope_by_id_filtra_correctamente(): void
    {
        $licitacion = Licitacion::factory()->create();

        $resultados = Licitacion::byId($licitacion->codigo_licitacion)->get();

        $this->assertCount(1, $resultados);
        $this->assertSame($licitacion->codigo_licitacion, $resultados->first()->codigo_licitacion);
    }

    public function test_scope_by_responsable_filtra_correctamente(): void
    {
        $r1 = Responsable::factory()->create();
        $r2 = Responsable::factory()->create();

        Licitacion::factory()->count(2)->create(['responsable_id' => $r1->id]);
        Licitacion::factory()->create(['responsable_id' => $r2->id]);

        $resultados = Licitacion::byResponsable((string) $r1->id)->get();

        $this->assertCount(2, $resultados);
    }

    public function test_scope_by_date_range_con_ambas_fechas(): void
    {
        Licitacion::factory()->create([
            'fecha_inicio' => '2026-06-01',
            'fecha_cierre' => '2026-06-30',
        ]);
        Licitacion::factory()->create([
            'fecha_inicio' => '2026-07-01',
            'fecha_cierre' => '2026-07-31',
        ]);

        // Buscar superposición con junio
        $resultados = Licitacion::byDateRange('2026-06-01', '2026-06-30')->get();

        $this->assertCount(1, $resultados);
    }

    public function test_scope_by_date_range_solo_desde(): void
    {
        Licitacion::factory()->create([
            'fecha_inicio' => '2026-05-01',
            'fecha_cierre' => '2026-05-15',
        ]);
        Licitacion::factory()->create([
            'fecha_inicio' => '2026-06-01',
            'fecha_cierre' => '2026-06-30',
        ]);

        $resultados = Licitacion::byDateRange('2026-06-01', null)->get();

        $this->assertCount(1, $resultados);
    }

    public function test_scope_by_date_range_solo_hasta(): void
    {
        Licitacion::factory()->create([
            'fecha_inicio' => '2026-05-01',
            'fecha_cierre' => '2026-05-31',
        ]);
        Licitacion::factory()->create([
            'fecha_inicio' => '2026-06-01',
            'fecha_cierre' => '2026-06-30',
        ]);

        $resultados = Licitacion::byDateRange(null, '2026-05-31')->get();

        $this->assertCount(1, $resultados);
    }

    // ─────────────────────────────────────────
    //  Accesores
    // ─────────────────────────────────────────

    public function test_presupuesto_formateado_con_cop(): void
    {
        $licitacion = Licitacion::factory()->create([
            'presupuesto' => 150000000,
            'moneda'      => 'COP',
        ]);

        $this->assertStringContainsString('$150.000.000', $licitacion->presupuesto_formateado);
    }

    public function test_presupuesto_formateado_con_usd(): void
    {
        $licitacion = Licitacion::factory()->create([
            'presupuesto' => 50000,
            'moneda'      => 'USD',
        ]);

        $this->assertStringContainsString('US$50.000', $licitacion->presupuesto_formateado);
    }

    public function test_presupuesto_formateado_con_eur(): void
    {
        $licitacion = Licitacion::factory()->create([
            'presupuesto' => 25000,
            'moneda'      => 'EUR',
        ]);

        $this->assertStringContainsString('€25.000', $licitacion->presupuesto_formateado);
    }

    public function test_rango_fechas_formato_correcto(): void
    {
        $licitacion = Licitacion::factory()->create([
            'fecha_inicio' => '2026-06-01',
            'hora_inicio'  => '09:00',
            'fecha_cierre' => '2026-08-15',
            'hora_cierre'  => '17:00',
        ]);

        $this->assertSame(
            '01/06/2026 09:00 → 15/08/2026 17:00',
            $licitacion->rango_fechas
        );
    }

    // ─────────────────────────────────────────
    //  Relaciones
    // ─────────────────────────────────────────

    public function test_relacion_responsable(): void
    {
        $responsable = Responsable::factory()->create();
        $licitacion  = Licitacion::factory()->create([
            'responsable_id' => $responsable->id,
        ]);

        $this->assertTrue($licitacion->responsable->is($responsable));
    }

    public function test_relacion_creador(): void
    {
        $user       = User::factory()->create();
        $licitacion = Licitacion::factory()->create(['creador_id' => $user->id]);

        $this->assertTrue($licitacion->creador->is($user));
    }

    public function test_responsable_puede_ser_nulo(): void
    {
        $licitacion = Licitacion::factory()->create(['responsable_id' => null]);

        $this->assertNull($licitacion->responsable);
    }
}
