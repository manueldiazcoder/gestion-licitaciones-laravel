<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'estado',
        'responsable_id',
        'creador_id',
    ];

    public const ESTADOS = ['Borrador', 'Publicado', 'En evaluación', 'Adjudicado', 'Cancelado'];

    public const COLORES_ESTADO = [
        'Borrador'      => 'bg-secondary',
        'Publicado'     => 'bg-primary',
        'En evaluación' => 'bg-warning text-dark',
        'Adjudicado'    => 'bg-success',
        'Cancelado'     => 'bg-danger',
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
    //  Relaciones
    // ─────────────────────────────────────────

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Responsable::class, 'responsable_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creador_id');
    }

    // ─────────────────────────────────────────
    //  Accesores
    // ─────────────────────────────────────────

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

    public function getRangoFechasAttribute(): string
    {
        return "{$this->fecha_inicio->format('d/m/Y')} {$this->hora_inicio} → {$this->fecha_cierre->format('d/m/Y')} {$this->hora_cierre}";
    }

    // ─────────────────────────────────────────
    //  Scopes
    // ─────────────────────────────────────────

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

    public function scopeByMoneda($query, ?string $moneda)
    {
        if ($moneda) {
            $query->where('moneda', $moneda);
        }
    }

    public function scopeByDateRange($query, ?string $desde, ?string $hasta)
    {
        if ($desde) {
            $query->whereDate('fecha_inicio', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('fecha_cierre', '<=', $hasta);
        }
    }

    public function scopeByEstado($query, ?string $estado)
    {
        if ($estado) {
            $query->where('estado', $estado);
        }
    }
}
