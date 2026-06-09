<?php

namespace App\Http\Controllers;

use App\Models\Proceso;
use App\Http\Requests\StoreProcesoRequest;
use App\Http\Requests\UpdateProcesoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcesoController extends Controller
{
    public function __construct()
    {
        // Visor solo puede ver (index + show).
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    /**
     * Listado de procesos con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $procesos = Proceso::query()
            ->search($request->query('search'))
            ->byMoneda($request->query('moneda'))
            ->byDateRange($request->query('desde'), $request->query('hasta'))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('procesos.index', compact('procesos'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        return view('procesos.create');
    }

    /**
     * Guardar un proceso nuevo.
     */
    public function store(StoreProcesoRequest $request)
    {
        $proceso = Proceso::create($request->validated());

        Log::info('Proceso creado', [
            'codigo' => $proceso->codigo_proceso,
            'objeto' => $proceso->objeto,
            'user'   => auth()->user()->email,
        ]);

        return redirect()
            ->route('procesos.show', $proceso)
            ->with('success', '✅ Proceso creado correctamente.');
    }

    /**
     * Ver detalle de un proceso.
     */
    public function show(Proceso $proceso)
    {
        return view('procesos.show', compact('proceso'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Proceso $proceso)
    {
        return view('procesos.edit', compact('proceso'));
    }

    /**
     * Actualizar un proceso existente.
     */
    public function update(UpdateProcesoRequest $request, Proceso $proceso)
    {
        $proceso->update($request->validated());

        Log::info('Proceso actualizado', [
            'codigo' => $proceso->codigo_proceso,
            'user'   => auth()->user()->email,
        ]);

        return redirect()
            ->route('procesos.show', $proceso)
            ->with('success', '✅ Proceso actualizado correctamente.');
    }

    /**
     * Eliminar un proceso.
     */
    public function destroy(Proceso $proceso)
    {
        $proceso->delete();

        Log::info('Proceso eliminado', [
            'codigo' => $proceso->codigo_proceso,
            'objeto' => $proceso->objeto,
            'user'   => auth()->user()->email,
        ]);

        return redirect()
            ->route('procesos.index')
            ->with('success', '✅ Proceso eliminado correctamente.');
    }
}
