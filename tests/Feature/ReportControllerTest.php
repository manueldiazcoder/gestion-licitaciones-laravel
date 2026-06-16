<?php

namespace Tests\Feature;

use App\Models\Licitacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Acceso a reportes
    // ─────────────────────────────────────────

    public function test_admin_puede_ver_reportes(): void
    {
        $admin = User::factory()->admin()->create();
        Licitacion::factory()->count(5)->create();

        $this->actingAs($admin)
            ->get(route('reportes.index'))
            ->assertStatus(200)
            ->assertSee('Reportes')
            ->assertSee('Total Licitaciones')
            ->assertSee('Presupuesto Global')
            ->assertSee('Licitaciones por Estado');
    }

    public function test_visor_puede_ver_reportes(): void
    {
        $visor = User::factory()->visor()->create();
        Licitacion::factory()->count(3)->create();

        $this->actingAs($visor)
            ->get(route('reportes.index'))
            ->assertStatus(200)
            ->assertSee('Reportes');
    }

    public function test_invitado_no_puede_ver_reportes(): void
    {
        $this->get(route('reportes.index'))
            ->assertRedirect(route('login'));
    }

    // ─────────────────────────────────────────
    //  Dashboard: datos correctos
    // ─────────────────────────────────────────

    public function test_reportes_muestra_estadisticas_correctas(): void
    {
        $admin = User::factory()->admin()->create();

        Licitacion::factory()->create(['moneda' => 'COP', 'presupuesto' => 1000000]);
        Licitacion::factory()->create(['moneda' => 'COP', 'presupuesto' => 2000000]);
        Licitacion::factory()->create(['moneda' => 'USD', 'presupuesto' => 50000]);

        $this->actingAs($admin)
            ->get(route('reportes.index'))
            ->assertStatus(200)
            ->assertSee('Total Licitaciones')
            ->assertSee('COP')
            ->assertSee('USD');
    }

    public function test_reportes_tablas_por_estado_muestran_datos(): void
    {
        $admin = User::factory()->admin()->create();

        Licitacion::factory()->count(2)->create(['estado' => 'Publicado']);
        Licitacion::factory()->create(['estado' => 'Borrador']);
        Licitacion::factory()->create(['estado' => 'Adjudicado']);

        $this->actingAs($admin)
            ->get(route('reportes.index'))
            ->assertStatus(200)
            ->assertSee('Publicado')
            ->assertSee('Borrador')
            ->assertSee('Adjudicado')
            ->assertDontSee('No hay licitaciones registradas');
    }
}
