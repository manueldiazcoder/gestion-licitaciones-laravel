<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResponsableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $responsables = Responsable::withCount('procesos')
            ->orderBy('nombre')
            ->paginate(20);

        return view('responsables.index', compact('responsables'));
    }

    public function create(): View
    {
        return view('responsables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'cargo'    => 'nullable|string|max:255',
            'email'    => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
        ]);

        Responsable::create($validated);

        return redirect()->route('responsables.index')
            ->with('success', 'Responsable creado correctamente.');
    }

    public function edit(Responsable $responsable): View
    {
        return view('responsables.edit', compact('responsable'));
    }

    public function update(Request $request, Responsable $responsable): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'cargo'    => 'nullable|string|max:255',
            'email'    => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
        ]);

        $responsable->update($validated);

        return redirect()->route('responsables.index')
            ->with('success', 'Responsable actualizado correctamente.');
    }

    public function destroy(Responsable $responsable): RedirectResponse
    {
        if ($responsable->procesos()->count() > 0) {
            return redirect()->route('responsables.index')
                ->with('error', 'No se puede eliminar un responsable con procesos asociados.');
        }

        $responsable->delete();

        return redirect()->route('responsables.index')
            ->with('success', 'Responsable eliminado correctamente.');
    }
}
