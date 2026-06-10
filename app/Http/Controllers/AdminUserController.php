<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(): View
    {
        $usuarios = User::withCount('procesosCreados')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function edit(User $user): View
    {
        return view('admin.usuarios.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,visor',
        ]);

        $user->update($validated);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario {$user->name} actualizado correctamente.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No podés eliminarte a vos mismo.');
        }

        if ($user->procesosCreados()->count() > 0) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No se puede eliminar un usuario con procesos asociados.');
        }

        $user->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
