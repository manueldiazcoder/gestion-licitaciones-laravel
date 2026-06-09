<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * Redirige al usuario al proveedor OAuth (Google o GitHub).
     */
    public function redirect(string $provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            return redirect()->route('login')->withErrors([
                'provider' => 'Proveedor de autenticación no válido.',
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Procesa el callback del proveedor OAuth.
     */
    public function callback(string $provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            return redirect()->route('login')->withErrors([
                'provider' => 'Proveedor de autenticación no válido.',
            ]);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            Log::error("Error en callback OAuth ({$provider})", [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'oauth' => "No se pudo autenticar con {$provider}. Intentalo de nuevo.",
            ]);
        }

        // Buscar usuario existente por provider + provider_id
        $user = User::where('provider', $provider)
                    ->where('provider_id', $socialUser->getId())
                    ->first();

        if (!$user) {
            // Buscar por email (por si ya se registró con email+password)
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Vincular la cuenta OAuth al usuario existente
                $user->update([
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar'      => $socialUser->getAvatar(),
                ]);

                Log::info("Cuenta OAuth vinculada a usuario existente", [
                    'email'    => $user->email,
                    'provider' => $provider,
                ]);
            } else {
                // Crear usuario nuevo
                $name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'Usuario';
                $email = $socialUser->getEmail();

                if (!$email) {
                    return redirect()->route('register')->withErrors([
                        'oauth' => "{$provider} no proporcionó un correo electrónico. Registrate manualmente.",
                    ]);
                }

                $user = User::create([
                    'name'        => $name,
                    'email'       => $email,
                    'password'    => bcrypt(\Illuminate\Support\Str::random(32)),
                    'role'        => 'visor',
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar'      => $socialUser->getAvatar(),
                ]);

                Log::info("Usuario nuevo registrado via OAuth", [
                    'email'    => $email,
                    'provider' => $provider,
                ]);
            }
        }

        // Iniciar sesión
        Auth::login($user, true);

        Log::info("Login exitoso via {$provider}", [
            'email' => $user->email,
            'role'  => $user->role,
        ]);

        return redirect()->intended('/');
    }
}
