<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            Log::info('Login exitoso', [
                'email' => $request->email,
                'role'  => Auth::user()->role,
            ]);

            return redirect()->intended('/');
        }

        Log::warning('Intento de login fallido', [
            'email' => $request->email,
        ]);

        return back()->withErrors([
            'email' => 'Las credenciales ingresadas no son correctas.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request)
    {
        Log::info('Logout', [
            'email' => Auth::user()->email ?? 'desconocido',
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Muestra el formulario de registro.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Procesa el registro de un nuevo usuario.
     * Siempre se crea con rol 'visor'.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:users,name'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'visor',
        ]);

        Log::info('Nuevo usuario registrado', [
            'name'  => $user->name,
            'email' => $user->email,
        ]);

        return redirect('/login')
            ->with('status', 'Cuenta creada correctamente. Ya podés iniciar sesión.');
    }
}
