<?php

namespace Tests\Feature;

use App\Models\Proceso;
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
        Proceso::factory()->count(5)->create();

        $this->actingAs($admin)
            ->get(route('reportes.index'))
            ->assertStatus(200)
            ->assertSee('Reportes')
            ->assertSee('Total procesos')
            ->assertSee('Presupuesto total')
            ->assertSee('Distribución por moneda');
    }

    public function test_visor_puede_ver_reportes(): void
    {
        $visor = User::factory()->visor()->create();
        Proceso::factory()->count(3)->create();

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
    //  Export CSV
    // ─────────────────────────────────────────

    public function test_admin_puede_exportar_csv(): void
    {
        $admin = User::factory()->admin()->create();
        Proceso::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('reportes.export'))
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_visor_no_puede_exportar_csv(): void
    {
        $visor = User::factory()->visor()->create();

        $this->actingAs($visor)
            ->get(route('reportes.export'))
            ->assertStatus(403);
    }

    // ─────────────────────────────────────────
    //  Dashboard: datos correctos
    // ─────────────────────────────────────────

    public function test_reportes_muestra_estadisticas_correctas(): void
    {
        $admin = User::factory()->admin()->create();

        // Crear procesos específicos para verificar stats
        Proceso::factory()->create(['moneda' => 'COP', 'presupuesto' => 1000000]);
        Proceso::factory()->create(['moneda' => 'COP', 'presupuesto' => 2000000]);
        Proceso::factory()->create(['moneda' => 'USD', 'presupuesto' => 50000]);

        $this->actingAs($admin)
            ->get(route('reportes.index'))
            ->assertStatus(200)
            ->assertSee('Total procesos')
            ->assertSee('3')          // 3 procesos en total
            ->assertSee('COP')
            ->assertSee('USD');
    }
}
