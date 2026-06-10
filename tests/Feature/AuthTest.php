<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Público: página de login
    // ─────────────────────────────────────────

    public function test_muestra_formulario_login(): void
    {
        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSee('Iniciar Sesión')
            ->assertSee('Correo electrónico');
    }

    public function test_login_con_credenciales_validas(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'admin',
        ]);

        $this->post(route('login'), [
            'email'    => 'test@example.com',
            'password' => 'secret123',
        ])
            ->assertRedirect(route('home'));
    }

    public function test_login_con_contrasena_invalida(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $this->post(route('login'), [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ])
            ->assertSessionHasErrors('email');
    }

    public function test_login_con_email_inexistente(): void
    {
        $this->post(route('login'), [
            'email'    => 'noexiste@example.com',
            'password' => 'anything',
        ])
            ->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────
    //  Logout
    // ─────────────────────────────────────────

    public function test_usuario_autenticado_puede_cerrar_sesion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    // ─────────────────────────────────────────
    //  Registro
    // ─────────────────────────────────────────

    public function test_muestra_formulario_registro(): void
    {
        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('Registrarse');
    }

    public function test_registro_exitoso_crea_usuario_como_visor(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Nuevo Usuario',
            'email'                 => 'nuevo@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
            'role'  => 'visor', // siempre visor al registrarse
        ]);

        // Después de registrar no queda autenticado (redirige al login)
        $this->assertGuest();
    }

    public function test_registro_valida_campos_obligatorios(): void
    {
        $this->post(route('register'), [])
            ->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_registro_valida_contrasena_minima(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Test',
            'email'                 => 'test@example.com',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    // ─────────────────────────────────────────
    //  Rutas protegidas redirigen a login
    // ─────────────────────────────────────────

    public function test_rutas_protegidas_redirigen_a_login(): void
    {
        $this->get(route('procesos.index'))->assertRedirect(route('login'));
        $this->get(route('procesos.create'))->assertRedirect(route('login'));
        $this->get(route('reportes.index'))->assertRedirect(route('login'));
    }
}
