<?php

namespace App\Http\Controllers;

use App\Models\Proceso;
use App\Models\Responsable;
use App\Http\Requests\StoreProcesoRequest;
use App\Http\Requests\UpdateProcesoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcesoController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except(['index', 'show']);
    }

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

    public function create()
    {
        $responsables = Responsable::orderBy('nombre')->get();
        return view('procesos.create', compact('responsables'));
    }

    public function store(StoreProcesoRequest $request)
    {
        $data = $request->validated();
        $data['creador_id'] = auth()->id();

        if (empty($data['estado'])) {
            $data['estado'] = 'Borrador';
        }

        $proceso = Proceso::create($data);

        Log::info('Proceso creado', [
            'codigo' => $proceso->codigo_proceso,
            'objeto' => $proceso->objeto,
            'user'   => auth()->user()->email,
        ]);

        return redirect()
            ->route('procesos.show', $proceso)
            ->with('success', 'Proceso creado correctamente.');
    }

    public function show(Proceso $proceso)
    {
        return view('procesos.show', compact('proceso'));
    }

    public function edit(Proceso $proceso)
    {
        $responsables = Responsable::orderBy('nombre')->get();
        return view('procesos.edit', compact('proceso', 'responsables'));
    }

    public function update(UpdateProcesoRequest $request, Proceso $proceso)
    {
        $proceso->update($request->validated());

        Log::info('Proceso actualizado', [
            'codigo' => $proceso->codigo_proceso,
            'user'   => auth()->user()->email,
        ]);

        return redirect()
            ->route('procesos.show', $proceso)
            ->with('success', 'Proceso actualizado correctamente.');
    }

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
            ->with('success', 'Proceso eliminado correctamente.');
    }
}
