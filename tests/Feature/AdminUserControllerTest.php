<?php

namespace Tests\Feature;

use App\Models\Licitacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Admin: acceso completo
    // ─────────────────────────────────────────

    public function test_admin_puede_ver_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('usuarios.index'))
            ->assertStatus(200)
            ->assertSee('Usuarios');
    }

    public function test_admin_ve_lista_de_usuarios(): void
    {
        $admin  = User::factory()->admin()->create(['name' => 'Admin Principal']);
        $visor  = User::factory()->visor()->create(['name' => 'Visor Uno']);
        $visor2 = User::factory()->visor()->create(['name' => 'Visor Dos']);

        $this->actingAs($admin)
            ->get(route('usuarios.index'))
            ->assertStatus(200)
            ->assertSee('Admin Principal')
            ->assertSee('Visor Uno')
            ->assertSee('Visor Dos');
    }

    public function test_admin_puede_ver_editar_formulario(): void
    {
        $admin   = User::factory()->admin()->create();
        $usuario = User::factory()->visor()->create();

        $this->actingAs($admin)
            ->get(route('usuarios.edit', $usuario))
            ->assertStatus(200)
            ->assertSee('Editar Usuario');
    }

    public function test_admin_puede_actualizar_usuario(): void
    {
        $admin   = User::factory()->admin()->create();
        $usuario = User::factory()->visor()->create();

        $this->actingAs($admin)
            ->put(route('usuarios.update', $usuario), [
                'name'  => 'Nombre Actualizado',
                'email' => 'actualizado@example.com',
                'role'  => 'admin',
            ])
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('users', [
            'id'    => $usuario->id,
            'name'  => 'Nombre Actualizado',
            'email' => 'actualizado@example.com',
            'role'  => 'admin',
        ]);
    }

    public function test_admin_puede_cambiar_role_de_admin_a_visor(): void
    {
        $admin   = User::factory()->admin()->create();
        $usuario = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('usuarios.update', $usuario), [
                'name'  => $usuario->name,
                'email' => $usuario->email,
                'role'  => 'visor',
            ])
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('users', [
            'id'   => $usuario->id,
            'role' => 'visor',
        ]);
    }

    public function test_admin_puede_eliminar_usuario_sin_licitaciones(): void
    {
        $admin   = User::factory()->admin()->create();
        $usuario = User::factory()->visor()->create();

        $this->actingAs($admin)
            ->delete(route('usuarios.destroy', $usuario))
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $usuario->id,
        ]);
    }

    public function test_admin_no_puede_eliminarse_a_si_mismo(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('usuarios.destroy', $admin))
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);

        $this->assertNotNull(session('error'));
    }

    public function test_admin_no_puede_eliminar_usuario_con_licitaciones(): void
    {
        $admin   = User::factory()->admin()->create();
        $usuario = User::factory()->visor()->create();
        Licitacion::factory()->create(['creador_id' => $usuario->id]);

        $this->actingAs($admin)
            ->delete(route('usuarios.destroy', $usuario))
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
        ]);

        $this->assertNotNull(session('error'));
    }

    // ─────────────────────────────────────────
    //  Visor: sin acceso
    // ─────────────────────────────────────────

    public function test_visor_no_puede_ver_index(): void
    {
        User::factory()->admin()->create(); // existe pero no es quien accede
        $visor = User::factory()->visor()->create();

        $this->actingAs($visor)
            ->get(route('usuarios.index'))
            ->assertStatus(403);
    }

    public function test_visor_no_puede_ver_editar_formulario(): void
    {
        $visor   = User::factory()->visor()->create();
        $usuario = User::factory()->admin()->create();

        $this->actingAs($visor)
            ->get(route('usuarios.edit', $usuario))
            ->assertStatus(403);
    }

    public function test_visor_no_puede_actualizar_usuario(): void
    {
        $visor   = User::factory()->visor()->create();
        $usuario = User::factory()->admin()->create();

        $this->actingAs($visor)
            ->put(route('usuarios.update', $usuario), [
                'name'  => 'No debería pasar',
                'email' => $usuario->email,
                'role'  => 'admin',
            ])
            ->assertStatus(403);
    }

    public function test_visor_no_puede_eliminar_usuario(): void
    {
        $visor   = User::factory()->visor()->create();
        $usuario = User::factory()->admin()->create();

        $this->actingAs($visor)
            ->delete(route('usuarios.destroy', $usuario))
            ->assertStatus(403);
    }

    // ─────────────────────────────────────────
    //  Validación
    // ─────────────────────────────────────────

    public function test_actualizacion_requiere_campos_obligatorios(): void
    {
        $admin   = User::factory()->admin()->create();
        $usuario = User::factory()->visor()->create();

        $this->actingAs($admin)
            ->put(route('usuarios.update', $usuario), [])
            ->assertSessionHasErrors(['name', 'email', 'role']);
    }

    public function test_actualizacion_valida_role(): void
    {
        $admin   = User::factory()->admin()->create();
        $usuario = User::factory()->visor()->create();

        $this->actingAs($admin)
            ->put(route('usuarios.update', $usuario), [
                'name'  => 'Test',
                'email' => 'test@example.com',
                'role'  => 'superadmin',
            ])
            ->assertSessionHasErrors('role');
    }

    public function test_actualizacion_valida_email_unico(): void
    {
        $admin    = User::factory()->admin()->create(['email' => 'admin@example.com']);
        $usuario  = User::factory()->visor()->create();
        User::factory()->create(['email' => 'existente@example.com']);

        $this->actingAs($admin)
            ->put(route('usuarios.update', $usuario), [
                'name'  => 'Test',
                'email' => 'existente@example.com',
                'role'  => 'visor',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_actualizacion_permite_mismo_email_del_usuario(): void
    {
        $admin   = User::factory()->admin()->create();
        $usuario = User::factory()->visor()->create(['email' => 'mio@example.com']);

        $this->actingAs($admin)
            ->put(route('usuarios.update', $usuario), [
                'name'  => 'Test Update',
                'email' => 'mio@example.com', // mismo email
                'role'  => 'visor',
            ])
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('users', [
            'id'    => $usuario->id,
            'name'  => 'Test Update',
            'email' => 'mio@example.com',
        ]);
    }

    // ─────────────────────────────────────────
    //  Invitado: redirige a login
    // ─────────────────────────────────────────

    public function test_invitado_redirigido_a_login(): void
    {
        $this->get(route('usuarios.index'))->assertRedirect(route('login'));
    }
}
