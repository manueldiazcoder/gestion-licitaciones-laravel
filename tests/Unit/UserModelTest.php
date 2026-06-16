<?php

namespace Tests\Unit;

use App\Models\Licitacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Métodos de roles
    // ─────────────────────────────────────────

    public function test_es_admin_retorna_true_cuando_role_es_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertTrue($user->esAdmin());
    }

    public function test_es_admin_retorna_false_cuando_role_no_es_admin(): void
    {
        $user = User::factory()->visor()->create();

        $this->assertFalse($user->esAdmin());
    }

    public function test_es_visor_retorna_true_cuando_role_es_visor(): void
    {
        $user = User::factory()->visor()->create();

        $this->assertTrue($user->esVisor());
    }

    public function test_es_visor_retorna_false_cuando_role_no_es_visor(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertFalse($user->esVisor());
    }

    // ─────────────────────────────────────────
    //  getRolFormateado
    // ─────────────────────────────────────────

    public function test_get_rol_formateado_retorna_administrador_para_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertSame('Administrador', $user->getRolFormateado());
    }

    public function test_get_rol_formateado_retorna_visor_para_visor(): void
    {
        $user = User::factory()->visor()->create();

        $this->assertSame('Visor', $user->getRolFormateado());
    }

    public function test_get_rol_formateado_retorna_nombre_del_role_si_no_es_estandar(): void
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $this->assertSame('Superadmin', $user->getRolFormateado());
    }

    // ─────────────────────────────────────────
    //  getRolBadge
    // ─────────────────────────────────────────

    public function test_get_rol_badge_contiene_span(): void
    {
        $user = User::factory()->admin()->create();

        $badge = $user->getRolBadge();

        $this->assertStringStartsWith('<span', $badge);
        $this->assertStringEndsWith('</span>', $badge);
    }

    public function test_get_rol_badge_para_admin_tiene_clase_bg_danger(): void
    {
        $user = User::factory()->admin()->create();

        $badge = $user->getRolBadge();

        $this->assertStringContainsString('bg-danger', $badge);
        $this->assertStringContainsString('Administrador', $badge);
    }

    public function test_get_rol_badge_para_visor_tiene_clase_bg_info(): void
    {
        $user = User::factory()->visor()->create();

        $badge = $user->getRolBadge();

        $this->assertStringContainsString('bg-info', $badge);
        $this->assertStringContainsString('text-dark', $badge);
        $this->assertStringContainsString('Visor', $badge);
    }

    // ─────────────────────────────────────────
    //  Relaciones
    // ─────────────────────────────────────────

    public function test_relacion_licitaciones_creadas_retorna_coleccion_vacia_sin_licitaciones(): void
    {
        $user = User::factory()->create();

        $this->assertCount(0, $user->licitacionesCreadas);
    }

    public function test_relacion_licitaciones_creadas_retorna_licitaciones_del_usuario(): void
    {
        $user = User::factory()->create();
        Licitacion::factory()->count(3)->create(['creador_id' => $user->id]);
        Licitacion::factory()->create(); // de otro usuario

        $this->assertCount(3, $user->licitacionesCreadas);
    }

}
