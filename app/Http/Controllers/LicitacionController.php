<?php

namespace App\Http\Controllers;

use App\Models\Licitacion;
use App\Models\Responsable;
use App\Http\Requests\StoreLicitacionRequest;
use App\Http\Requests\UpdateLicitacionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LicitacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except(['index', 'show', 'exportCsv']);
    }

    public function index(Request $request)
    {
        $licitaciones = Licitacion::query()
            ->search($request->query('search'))
            ->byId($request->query('codigo'))
            ->byResponsable($request->query('responsable_id'))
            ->byEstado($request->query('estado'))
            ->byDateRange($request->query('desde'), $request->query('hasta'))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $responsables = Responsable::orderBy('nombre_completo')->get(['id', 'nombre_completo']);

        return view('licitaciones.index', compact('licitaciones', 'responsables'));
    }

    public function create()
    {
        $responsables = Responsable::orderBy('nombre_completo')->get();
        return view('licitaciones.create', compact('responsables'));
    }

    public function store(StoreLicitacionRequest $request)
    {
        $data = $request->validated();
        $data['creador_id'] = auth()->id();

        if (empty($data['estado'])) {
            $data['estado'] = 'Borrador';
        }

        $licitacion = Licitacion::create($data);

        Log::info('Licitación creada', [
            'codigo' => $licitacion->codigo_licitacion,
            'objeto' => $licitacion->objeto,
            'user'   => auth()->user()->email,
        ]);

        return redirect()
            ->route('licitaciones.show', $licitacion)
            ->with('success', 'Licitación creada correctamente.');
    }

    public function show(Licitacion $licitacion)
    {
        return view('licitaciones.show', compact('licitacion'));
    }

    public function edit(Licitacion $licitacion)
    {
        $responsables = Responsable::orderBy('nombre_completo')->get();
        return view('licitaciones.edit', compact('licitacion', 'responsables'));
    }

    public function update(UpdateLicitacionRequest $request, Licitacion $licitacion)
    {
        $licitacion->update($request->validated());

        Log::info('Licitación actualizada', [
            'codigo' => $licitacion->codigo_licitacion,
            'user'   => auth()->user()->email,
        ]);

        return redirect()
            ->route('licitaciones.show', $licitacion)
            ->with('success', 'Licitación actualizada correctamente.');
    }

    public function exportCsv(Request $request)
    {
        $licitaciones = Licitacion::query()
            ->search($request->query('search'))
            ->byId($request->query('codigo'))
            ->byResponsable($request->query('responsable_id'))
            ->byEstado($request->query('estado'))
            ->byDateRange($request->query('desde'), $request->query('hasta'))
            ->orderBy('codigo_licitacion')
            ->get();

        Log::info('Exportación CSV', [
            'total_registros' => $licitaciones->count(),
            'user'            => auth()->user()->email,
        ]);

        $filename = 'licitaciones_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($licitaciones) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM Excel

            fputcsv($output, [
                'ID', 'Objeto', 'Descripción', 'Estado',
                'Fecha Inicio', 'Hora Inicio', 'Fecha Cierre', 'Hora Cierre',
                'Presupuesto', 'Moneda', 'Responsable',
            ]);

            foreach ($licitaciones as $p) {
                fputcsv($output, [
                    $p->codigo_licitacion,
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

    public function destroy(Licitacion $licitacion)
    {
        $licitacion->delete();

        Log::info('Licitación eliminada', [
            'codigo' => $licitacion->codigo_licitacion,
            'objeto' => $licitacion->objeto,
            'user'   => auth()->user()->email,
        ]);

        return redirect()
            ->route('licitaciones.index')
            ->with('success', 'Licitación eliminada correctamente.');
    }
}
