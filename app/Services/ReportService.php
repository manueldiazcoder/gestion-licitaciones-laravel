<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Licitacion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ReportService
{
    /**
     * Genera todos los datos necesarios para la vista de reportes.
     *
     * @return array<string, mixed>
     */
    public function generate(): array
    {
        $totalLicitaciones    = 0;
        $publicados           = 0;
        $enEvaluacion         = 0;
        $adjudicados          = 0;
        $presupuestoTotal     = 0;
        $promedioPresupuesto  = 0;
        $porMoneda            = collect();
        $proximosCerrar       = collect();
        $licitacionesPorEstado = collect();
        $presupuestoPorEstado  = collect();

        try {
            $totalLicitaciones   = Licitacion::count();
            $publicados          = Licitacion::where('estado', 'Publicado')->count();
            $enEvaluacion        = Licitacion::where('estado', 'En evaluación')->count();
            $adjudicados         = Licitacion::where('estado', 'Adjudicado')->count();
            $presupuestoTotal    = Licitacion::sum('presupuesto');
            $promedioPresupuesto = $totalLicitaciones > 0
                ? round((float) Licitacion::avg('presupuesto'), 2)
                : 0;

            $licitacionesPorEstado = $this->buildEstadoSummary();
            $presupuestoPorEstado  = $this->buildBudgetByEstado();
            $porMoneda             = $this->buildCurrencyDistribution();
            $proximosCerrar        = $this->buildUpcomingClosures();

        } catch (\Throwable $e) {
            Log::warning('ReportService: error generando reportes', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile() . ':' . $e->getLine(),
            ]);
        }

        return compact(
            'totalLicitaciones',
            'publicados',
            'enEvaluacion',
            'adjudicados',
            'presupuestoTotal',
            'promedioPresupuesto',
            'porMoneda',
            'proximosCerrar',
            'licitacionesPorEstado',
            'presupuestoPorEstado',
        );
    }

    /**
     * Construye resumen de cantidad de licitaciones por estado.
     * Asegura que todos los estados aparezcan (incluso con 0).
     */
    private function buildEstadoSummary(): Collection
    {
        $dbRows = Licitacion::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->orderBy('estado')
            ->get()
            ->keyBy('estado');

        $result = collect();

        foreach (Licitacion::ESTADOS as $est) {
            $result->push([
                'estado' => $est,
                'total'  => $dbRows->has($est) ? (int) $dbRows[$est]->total : 0,
            ]);
        }

        return $result;
    }

    /**
     * Construye resumen de presupuesto por estado.
     * Asegura que todos los estados aparezcan (incluso con 0).
     */
    private function buildBudgetByEstado(): Collection
    {
        $budgetRows = Licitacion::selectRaw("estado, COUNT(*) as cantidad, COALESCE(SUM(presupuesto), 0) as presupuesto_total")
            ->groupBy('estado')
            ->orderBy('estado')
            ->get()
            ->keyBy('estado');

        $result = collect();

        foreach (Licitacion::ESTADOS as $est) {
            $result->push([
                'estado'           => $est,
                'cantidad'         => $budgetRows->has($est) ? (int) $budgetRows[$est]->cantidad : 0,
                'presupuesto_total' => $budgetRows->has($est) ? (float) $budgetRows[$est]->presupuesto_total : 0,
            ]);
        }

        return $result;
    }

    /**
     * Distribución de licitaciones por moneda.
     */
    private function buildCurrencyDistribution(): Collection
    {
        return Licitacion::selectRaw(
            "moneda,
             COUNT(*)                               as total,
             SUM(presupuesto)                        as presupuesto,
             ROUND(AVG(presupuesto), 2)              as promedio,
             ROUND(COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM licitaciones), 0), 1) as porcentaje"
        )
            ->groupBy('moneda')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Licitaciones próximas a cerrar (próximos 30 días).
     */
    private function buildUpcomingClosures(): Collection
    {
        return Licitacion::whereDate('fecha_cierre', '>=', now())
            ->whereDate('fecha_cierre', '<=', now()->addDays(30))
            ->orderBy('fecha_cierre')
            ->get();
    }
}
