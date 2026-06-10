<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\MailResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Forgot password
    // ─────────────────────────────────────────

    public function test_muestra_formulario_olvide_contrasena(): void
    {
        $this->get(route('password.request'))
            ->assertStatus(200)
            ->assertSee('Recuperar contraseña')
            ->assertSee('Enviar enlace');
    }

    public function test_envia_enlace_reset_a_usuario_existente(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ])->assertSessionHas('status');

        Notification::assertSentTo(
            $user,
            MailResetPasswordNotification::class
        );
    }

    public function test_no_envia_enlace_a_email_inexistente(): void
    {
        Notification::fake();

        $this->post(route('password.email'), [
            'email' => 'noexiste@example.com',
        ])->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    }

    public function test_valida_email_requerido(): void
    {
        $this->post(route('password.email'), [])
            ->assertSessionHasErrors('email');
    }

    // ─────────────────────────────────────────
    //  Reset password
    // ─────────────────────────────────────────

    public function test_muestra_formulario_reset_con_token(): void
    {
        $this->get(route('password.reset', ['token' => 'fake-token']))
            ->assertStatus(200)
            ->assertSee('Restablecer contraseña');
    }

    public function test_usuario_puede_restablecer_contrasena(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Send reset link
        $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        // Extract the notification and get the token
        $notification = Notification::sent($user, MailResetPasswordNotification::class)->first();
        $token = $notification->token;

        // Reset with valid token
        $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => 'test@example.com',
            'password'              => 'nueva-contraseña',
            'password_confirmation' => 'nueva-contraseña',
        ])->assertRedirect(route('login'));
    }

    public function test_reset_falla_con_token_invalido(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->post(route('password.update'), [
            'token'                 => 'token-invalido',
            'email'                 => 'test@example.com',
            'password'              => 'nueva-contraseña',
            'password_confirmation' => 'nueva-contraseña',
        ])->assertSessionHasErrors('email');
    }

    public function test_reset_valida_contrasena_minima(): void
    {
        $this->post(route('password.update'), [
            'token'                 => 'token',
            'email'                 => 'test@example.com',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }
}
