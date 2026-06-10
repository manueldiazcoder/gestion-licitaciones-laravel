@extends('layouts.base')

@section('title', "Proceso #{$proceso->codigo_proceso}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-file-text me-2"></i>Proceso #{{ $proceso->codigo_proceso }}
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('procesos.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
        @if (Auth::user()?->esAdmin())
            <a href="{{ route('procesos.edit', $proceso) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
        @endif
    </div>
</div>

<div class="card shadow-sm border">
    <div class="card-body p-4">
        <div class="row g-3">
            {{-- Objeto --}}
            <div class="col-12">
                <label class="text-muted small text-uppercase fw-semibold">Objeto</label>
                <p class="fw-semibold mb-0 fs-5" style="color: var(--color-primary);">{{ $proceso->objeto }}</p>
            </div>

            {{-- Actividad --}}
            @if ($proceso->actividad)
                <div class="col-12">
                    <label class="text-muted small text-uppercase fw-semibold">Actividad</label>
                    <p class="mb-0">{{ $proceso->actividad }}</p>
                </div>
            @endif

            {{-- Descripción --}}
            <div class="col-12">
                <label class="text-muted small text-uppercase fw-semibold">Descripción / Alcance</label>
                <p class="mb-0">{{ $proceso->descripcion }}</p>
            </div>

            <hr class="delimitadorSuperior">

            {{-- Estado --}}
            <div class="col-md-4">
                <label class="text-muted small text-uppercase fw-semibold">Estado</label>
                <p class="mb-0">
                    @if ($proceso->estado)
                        <span class="badge bg-{{ $proceso->estado === 'activo' ? 'success' : ($proceso->estado === 'evaluacion' ? 'warning' : ($proceso->estado === 'adjudicado' ? 'info' : 'secondary')) }}">
                            {{ ucfirst($proceso->estado) }}
                        </span>
                    @else
                        <span class="badge bg-secondary">Borrador</span>
                    @endif
                </p>
            </div>

            {{-- Presupuesto y moneda --}}
            <div class="col-md-4">
                <label class="text-muted small text-uppercase fw-semibold">Presupuesto</label>
                <p class="h5 mb-0 fw-bold">{{ $proceso->presupuesto_formateado }}</p>
            </div>
            <div class="col-md-4">
                <label class="text-muted small text-uppercase fw-semibold">Moneda</label>
                <p class="mb-0">{{ $proceso->moneda }}</p>
            </div>

            <hr class="delimitadorSuperior">

            {{-- Responsable --}}
            <div class="col-md-4">
                <label class="text-muted small text-uppercase fw-semibold">Responsable</label>
                <p class="mb-0">{{ $proceso->responsable?->nombre ?? '—' }}</p>
            </div>

            {{-- Fechas --}}
            <div class="col-md-4">
                <label class="text-muted small text-uppercase fw-semibold">Fecha de inicio</label>
                <p class="mb-0">{{ $proceso->fecha_inicio->format('d/m/Y') }}</p>
                <small class="text-muted">{{ $proceso->hora_inicio }}</small>
            </div>
            <div class="col-md-4">
                <label class="text-muted small text-uppercase fw-semibold">Fecha de cierre</label>
                <p class="mb-0">{{ $proceso->fecha_cierre->format('d/m/Y') }}</p>
                <small class="text-muted">{{ $proceso->hora_cierre }}</small>
            </div>

            <hr class="delimitadorSuperior">

            {{-- Metadatos --}}
            <div class="col-md-6">
                <label class="text-muted small text-uppercase fw-semibold">Creado por</label>
                <p class="mb-0 small">{{ $proceso->creador?->name ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <label class="text-muted small text-uppercase fw-semibold">Creado</label>
                <p class="mb-0 small">{{ $proceso->created_at?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
