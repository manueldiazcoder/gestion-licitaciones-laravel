<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    use HasFactory;

    protected $primaryKey = 'codigo_proceso';

    protected $fillable = [
        'objeto',
        'actividad',
        'descripcion',
        'moneda',
        'presupuesto',
        'fecha_inicio',
        'hora_inicio',
        'fecha_cierre',
        'hora_cierre',
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_cierre' => 'date:Y-m-d',
        'hora_inicio'  => 'string',
        'hora_cierre'  => 'string',
        'presupuesto'  => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // ─────────────────────────────────────────
    //  Accesores
    // ─────────────────────────────────────────

    /**
     * Presupuesto formateado con símbolo de moneda.
     */
    public function getPresupuestoFormateadoAttribute(): string
    {
        $simbolos = [
            'COP' => '$',
            'USD' => 'US$',
            'EUR' => '€',
        ];

        $simbolo = $simbolos[$this->moneda] ?? $this->moneda . ' ';
        $monto   = number_format($this->presupuesto, 0, ',', '.');

        return "{$simbolo}{$monto}";
    }

    /**
     * Retorna el rango de fechas como texto legible.
     */
    public function getRangoFechasAttribute(): string
    {
        return "{$this->fecha_inicio->format('d/m/Y')} {$this->hora_inicio} → {$this->fecha_cierre->format('d/m/Y')} {$this->hora_cierre}";
    }

    // ─────────────────────────────────────────
    //  Scopes
    // ─────────────────────────────────────────

    /**
     * Filtrar por término de búsqueda (objeto, descripción o actividad).
     */
    public function scopeSearch($query, ?string $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('objeto', 'like', "%{$term}%")
                  ->orWhere('descripcion', 'like', "%{$term}%")
                  ->orWhere('actividad', 'like', "%{$term}%");
            });
        }
    }

    /**
     * Filtrar por moneda.
     */
    public function scopeByMoneda($query, ?string $moneda)
    {
        if ($moneda) {
            $query->where('moneda', $moneda);
        }
    }

    /**
     * Filtrar por rango de fechas.
     */
    public function scopeByDateRange($query, ?string $desde, ?string $hasta)
    {
        if ($desde) {
            $query->whereDate('fecha_inicio', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('fecha_cierre', '<=', $hasta);
        }
    }
}
