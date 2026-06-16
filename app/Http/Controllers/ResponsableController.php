<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ResponsableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $responsables = Responsable::withCount('licitaciones')
            ->orderBy('nombre_completo')
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
            'nombre_completo'    => 'required|string|max:255',
            'numero_telefono'    => 'required|string|max:30',
            'correo_electronico' => 'required|email|max:255',
        ]);

        Responsable::create($validated);

        Log::info('Responsable creado', [
            'nombre_completo' => $validated['nombre_completo'],
            'user'            => auth()->user()->email,
        ]);

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
            'nombre_completo'    => 'required|string|max:255',
            'numero_telefono'    => 'required|string|max:30',
            'correo_electronico' => 'required|email|max:255',
        ]);

        $responsable->update($validated);

        Log::info('Responsable actualizado', [
            'codigo'          => $responsable->codigo_responsable,
            'nombre_completo' => $validated['nombre_completo'],
            'user'            => auth()->user()->email,
        ]);

        return redirect()->route('responsables.index')
            ->with('success', 'Responsable actualizado correctamente.');
    }

    public function destroy(Responsable $responsable): RedirectResponse
    {
        if ($responsable->licitaciones()->count() > 0) {
            return redirect()->route('responsables.index')
                ->with('error', 'No se puede eliminar un responsable con licitaciones asociadas.');
        }

        $responsable->delete();

        Log::info('Responsable eliminado', [
            'codigo'          => $responsable->codigo_responsable,
            'nombre_completo' => $responsable->nombre_completo,
            'user'            => auth()->user()->email,
        ]);

        return redirect()->route('responsables.index')
            ->with('success', 'Responsable eliminado correctamente.');
    }
}
