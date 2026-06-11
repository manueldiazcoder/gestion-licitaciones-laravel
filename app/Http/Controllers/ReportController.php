<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Licitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->only('exportCsv');
    }

    /**
     * Dashboard de estadísticas.
     */
    public function index()
    {
        // Valores por defecto seguros
        $totalLicitaciones      = 0;
        $publicados         = 0;
        $enEvaluacion       = 0;
        $adjudicados        = 0;
        $presupuestoTotal   = 0;
        $promedioPresupuesto = 0;
        $porMoneda          = collect();
        $proximosCerrar     = collect();
        $presupuestoPorMoneda = collect();
        $totalPorMoneda     = collect();
        $meses              = collect();

        try {
            $totalLicitaciones   = Licitacion::count();
            $publicados      = Licitacion::where('estado', 'Publicado')->count();
            $enEvaluacion    = Licitacion::where('estado', 'En evaluación')->count();
            $adjudicados     = Licitacion::where('estado', 'Adjudicado')->count();
            $presupuestoTotal = Licitacion::sum('presupuesto');
            $promedioPresupuesto = $totalLicitaciones > 0
                ? round(Licitacion::avg('presupuesto'), 2)
                : 0;

            // Distribución por moneda
            $porMoneda = Licitacion::selectRaw(
                "moneda,
                 COUNT(*)                               as total,
                 SUM(presupuesto)                        as presupuesto,
                 ROUND(AVG(presupuesto), 2)              as promedio,
                 ROUND(COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM licitaciones), 0), 1) as porcentaje"
            )
                ->groupBy('moneda')
                ->orderByDesc('total')
                ->get();

            // Próximos a cerrar (próximos 30 días)
            $proximosCerrar = Licitacion::whereDate('fecha_cierre', '>=', now())
                ->whereDate('fecha_cierre', '<=', now()->addDays(30))
                ->orderBy('fecha_cierre')
                ->get();

            // Presupuesto total por moneda para las cards
            $presupuestoPorMoneda = $porMoneda->pluck('presupuesto', 'moneda');
            $totalPorMoneda       = $porMoneda->pluck('total', 'moneda');

            $driver = DB::connection()->getDriverName();
            $dateExpr = $driver === 'sqlite'
                ? "strftime('%Y-%m', created_at)"
                : "DATE_FORMAT(created_at, '%Y-%m')";

            $meses = Licitacion::selectRaw(
                "{$dateExpr} as mes,
                 COUNT(*)     as total"
            )
                ->groupBy('mes')
                ->orderBy('mes')
                ->get();
        } catch (\Throwable $e) {
            // DB sin migraciones o sin datos — valores por defecto
        }

        return view('reportes.index', compact(
            'totalLicitaciones',
            'publicados',
            'enEvaluacion',
            'adjudicados',
            'presupuestoTotal',
            'promedioPresupuesto',
            'porMoneda',
            'proximosCerrar',
            'presupuestoPorMoneda',
            'totalPorMoneda',
            'meses',
        ));
    }

    /**
     * Exportar licitaciones a CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = Licitacion::query();

        // Filtros opcionales
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('moneda')) {
            $query->byMoneda($request->moneda);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_cierre', '<=', $request->fecha_hasta);
        }

        $licitaciones = $query->orderBy('fecha_inicio')->get();

        $filename = 'licitaciones_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($licitaciones) {
            $output = fopen('php://output', 'w');

            // BOM para que Excel lea bien UTF-8
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Cabeceras
            fputcsv($output, [
                'Código',
                'Objeto',
                'Actividad',
                'Descripción',
                'Fecha inicio',
                'Hora inicio',
                'Fecha cierre',
                'Hora cierre',
                'Presupuesto',
                'Moneda',
                'Creado',
            ]);

            foreach ($licitaciones as $p) {
                fputcsv($output, [
                    $p->codigo_licitacion,
                    $p->objeto,
                    $p->actividad,
                    $p->descripcion,
                    $p->fecha_inicio,
                    $p->hora_inicio,
                    $p->fecha_cierre,
                    $p->hora_cierre,
                    $p->presupuesto,
                    $p->moneda,
                    $p->created_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
