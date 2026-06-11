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
            ->byId($request->query('codigo'))
            ->byResponsable($request->query('responsable_id'))
            ->byEstado($request->query('estado'))
            ->byDateRange($request->query('desde'), $request->query('hasta'))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $responsables = Responsable::orderBy('nombre_completo')->get(['id', 'nombre_completo']);

        return view('procesos.index', compact('procesos', 'responsables'));
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

    public function exportCsv(Request $request)
    {
        $procesos = Proceso::query()
            ->search($request->query('search'))
            ->byId($request->query('codigo'))
            ->byResponsable($request->query('responsable_id'))
            ->byEstado($request->query('estado'))
            ->byDateRange($request->query('desde'), $request->query('hasta'))
            ->orderBy('codigo_proceso')
            ->get();

        $filename = 'procesos_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($procesos) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM Excel

            fputcsv($output, [
                'ID', 'Objeto', 'Descripción', 'Estado',
                'Fecha Inicio', 'Hora Inicio', 'Fecha Cierre', 'Hora Cierre',
                'Presupuesto', 'Moneda', 'Responsable',
            ]);

            foreach ($procesos as $p) {
                fputcsv($output, [
                    $p->codigo_proceso,
                    $p->objeto,
                    $p->descripcion,
                    $p->estado ?: '—',
                    $p->fecha_inicio?->format('Y-m-d'),
                    $p->hora_inicio,
                    $p->fecha_cierre?->format('Y-m-d'),
                    $p->hora_cierre,
                    $p->presupuesto,
                    $p->moneda,
                    $p->responsable?->nombre_completo ?: '—',
                ]);
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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
