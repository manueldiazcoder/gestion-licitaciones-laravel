<?php

namespace Tests\Feature;

use App\Models\Responsable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsableControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Admin: acceso completo
    // ─────────────────────────────────────────

    public function test_admin_puede_ver_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('responsables.index'))
            ->assertStatus(200)
            ->assertSee('Responsables');
    }

    public function test_admin_ve_lista_vacia_cuando_no_hay_responsables(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('responsables.index'))
            ->assertStatus(200)
            ->assertSee('No hay responsables registrados');
    }

    public function test_admin_puede_ver_crear_formulario(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('responsables.create'))
            ->assertStatus(200)
            ->assertSee('Nuevo Responsable');
    }

    public function test_admin_puede_crear_responsable(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('responsables.store'), [
                'nombre_completo'    => 'Juan Pérez',
                'numero_telefono'    => '3001234567',
                'correo_electronico' => 'juan@example.com',
            ])
            ->assertRedirect(route('responsables.index'));

        $this->assertDatabaseHas('responsables', [
            'nombre_completo'    => 'Juan Pérez',
            'numero_telefono'    => '3001234567',
            'correo_electronico' => 'juan@example.com',
        ]);
    }

    public function test_admin_puede_ver_editar_formulario(): void
    {
        $admin        = User::factory()->admin()->create();
        $responsable  = Responsable::factory()->create();

        $this->actingAs($admin)
            ->get(route('responsables.edit', $responsable))
            ->assertStatus(200)
            ->assertSee('Editar Responsable');
    }

    public function test_admin_puede_actualizar_responsable(): void
    {
        $admin       = User::factory()->admin()->create();
        $responsable = Responsable::factory()->create();

        $this->actingAs($admin)
            ->put(route('responsables.update', $responsable), [
                'nombre_completo'    => 'María García',
                'numero_telefono'    => '3117654321',
                'correo_electronico' => 'maria@example.com',
            ])
            ->assertRedirect(route('responsables.index'));

        $this->assertDatabaseHas('responsables', [
            'id'                 => $responsable->id,
            'nombre_completo'    => 'María García',
            'numero_telefono'    => '3117654321',
            'correo_electronico' => 'maria@example.com',
        ]);
    }

    public function test_admin_puede_eliminar_responsable_sin_licitaciones(): void
    {
        $admin       = User::factory()->admin()->create();
        $responsable = Responsable::factory()->create();

        $this->actingAs($admin)
            ->delete(route('responsables.destroy', $responsable))
            ->assertRedirect(route('responsables.index'));

        $this->assertDatabaseMissing('responsables', [
            'id' => $responsable->id,
        ]);
    }

    public function test_admin_no_puede_eliminar_responsable_con_licitaciones(): void
    {
        $admin       = User::factory()->admin()->create();
        $responsable = Responsable::factory()->create();
        \App\Models\Licitacion::factory()->create(['responsable_id' => $responsable->id]);

        $this->actingAs($admin)
            ->delete(route('responsables.destroy', $responsable))
            ->assertRedirect(route('responsables.index'));

        $this->assertDatabaseHas('responsables', [
            'id' => $responsable->id,
        ]);
    }

    public function test_admin_ve_mensaje_de_error_al_eliminar_responsable_con_licitaciones(): void
    {
        $admin       = User::factory()->admin()->create();
        $responsable = Responsable::factory()->create();
        \App\Models\Licitacion::factory()->create(['responsable_id' => $responsable->id]);

        $this->actingAs($admin)
            ->delete(route('responsables.destroy', $responsable))
            ->assertRedirect(route('responsables.index'));

        // Verifica que devuelve el mensaje de error en la sesión
        $this->assertNotNull(session('error'));
    }

    // ─────────────────────────────────────────
    //  Visor: sin acceso
    // ─────────────────────────────────────────

    public function test_visor_no_puede_ver_index(): void
    {
        $visor = User::factory()->visor()->create();

        $this->actingAs($visor)
            ->get(route('responsables.index'))
            ->assertStatus(403);
    }

    public function test_visor_no_puede_ver_crear_formulario(): void
    {
        $visor = User::factory()->visor()->create();

        $this->actingAs($visor)
            ->get(route('responsables.create'))
            ->assertStatus(403);
    }

    public function test_visor_no_puede_crear_responsable(): void
    {
        $visor = User::factory()->visor()->create();

        $this->actingAs($visor)
            ->post(route('responsables.store'), [
                'nombre_completo'    => 'Intruso',
                'numero_telefono'    => '000',
                'correo_electronico' => 'intruso@test.com',
            ])
            ->assertStatus(403);
    }

    public function test_visor_no_puede_ver_editar_formulario(): void
    {
        $visor       = User::factory()->visor()->create();
        $responsable = Responsable::factory()->create();

        $this->actingAs($visor)
            ->get(route('responsables.edit', $responsable))
            ->assertStatus(403);
    }

    public function test_visor_no_puede_actualizar_responsable(): void
    {
        $visor       = User::factory()->visor()->create();
        $responsable = Responsable::factory()->create();

        $this->actingAs($visor)
            ->put(route('responsables.update', $responsable), [
                'nombre_completo' => 'No debería pasar',
            ])
            ->assertStatus(403);
    }

    public function test_visor_no_puede_eliminar_responsable(): void
    {
        $visor       = User::factory()->visor()->create();
        $responsable = Responsable::factory()->create();

        $this->actingAs($visor)
            ->delete(route('responsables.destroy', $responsable))
            ->assertStatus(403);
    }

    // ─────────────────────────────────────────
    //  Validación
    // ─────────────────────────────────────────

    public function test_creacion_requiere_campos_obligatorios(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('responsables.store'), [])
            ->assertSessionHasErrors([
                'nombre_completo',
                'numero_telefono',
                'correo_electronico',
            ]);
    }

    public function test_creacion_valida_correo_invalido(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('responsables.store'), [
                'nombre_completo'    => 'Test',
                'numero_telefono'    => '123',
                'correo_electronico' => 'no-es-un-email',
            ])
            ->assertSessionHasErrors('correo_electronico');
    }

    public function test_creacion_valida_longitud_maxima(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('responsables.store'), [
                'nombre_completo'    => str_repeat('a', 256),
                'numero_telefono'    => str_repeat('1', 31),
                'correo_electronico' => 'valido@example.com',
            ])
            ->assertSessionHasErrors(['nombre_completo', 'numero_telefono']);
    }

    // ─────────────────────────────────────────
    //  Invitado: redirige a login
    // ─────────────────────────────────────────

    public function test_invitado_redirigido_a_login(): void
    {
        $this->get(route('responsables.index'))->assertRedirect(route('login'));
        $this->get(route('responsables.create'))->assertRedirect(route('login'));
        $this->post(route('responsables.store'), [])->assertRedirect(route('login'));
    }
}
